<template>
  <div>
    <div
        class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6"
        v-if="nodes.length === 0"
    >
      <div class="mx-auto max-w-screen-sm text-center">
        <p
            class="mb-4 text-2xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white"
        >
          Categories not found
        </p>
        <button
            @click="openModalToAddNewCategory"
            class="w-52 h-10 px-1 bg-blue-500 rounded-md text-white"
        >
          Add First Category
        </button>
      </div>
    </div>
    <TreeTable :value="nodes" tableStyle="min-width: 50rem" v-else>
      <template #header>
        <button
            @click="openModalToAddNewCategory"
            class="w-32 h-10 px-1 bg-blue-500 rounded-md text-white"
        >
          Add Category
        </button>
      </template>

      <Column field="name" header="Name" expander style="width: 250px">
        <template #body="slotProps">
          <span :class="{ 'font-semibold': isFirstNode(slotProps.node) }">
            {{ slotProps.node.data.name }}
            <span v-show="isFirstNode(slotProps.node)" class="text-red-500"
            >( Main )</span
            >
          </span>
        </template>
      </Column>

      <Column field="slug" header="Slug" style="width: 150px"></Column>

      <Column style="width: 10rem">
        <template #body="slotProps">
          <CategoriesActions
              :node="slotProps.node"
              @add-subcategory="openModalToAddSubcategory"
              @edit-category="openModalToEditCategory"
              @delete-category="initializeNodeDeletion"
          />
        </template>
      </Column>
    </TreeTable>

    <DefaultModal
        :isActive="isModalOpen"
        @close-modal="closeModal"
        max-width="max-w-2xl"
    >
      <template #modalHeading>
        {{ modalAction.title }}
      </template>
      <template #modalBody>
        <div class="my-8">
          <label
              for="categoryName"
              class="block text-sm text-start font-medium"
          >
            Name
          </label>
          <base-text-input
              id="categoryName"
              v-model="newCategory.name"
              class="mt-1 block w-full border rounded-md shadow-sm focus:ring focus:ring-opacity-50"
              placeholder="Enter category name"
              body-class="!p-2"
              name="categoryName"
          />
        </div>
        <div class="my-4">
          <add-attribute-to-category
              :category-id="modalAction.categoryId"
              :existingAttributes="categoryAttributes"
              :newAttributes="attributes"
              @update-existing-attribute="updateExistingAttributes"
              @update-new-attributes="updateAttributes"
          />
        </div>
        <div v-if="modalAction.icon" class="my-2">
          <label for="categoryIcon" class="block text-sm font-medium">
            Category Icon
          </label>
          <div v-if="modalAction.icon" class="my-2">
            <img
                :src="modalAction.icon"
                alt="Category Icon"
                class="w-16 h-16 mt-2"
            />
          </div>
        </div>
        <div
            v-if="!modalAction.icon || modalAction.btnName === 'Edit'"
            class="mb-8"
        >
          <label for="svgFile" v-if="modalAction.btnName === 'Edit'">
            Upload New Category Icon (SVG)
          </label>
          <label for="svgFile" class="block text-sm font-medium" v-else>
            Upload Category Icon (SVG)
          </label>
          <input
              type="file"
              id="svgFile"
              accept="image/svg+xml"
              @change="handleFileInput"
              class="mt-1 block w-full border rounded-md shadow-sm p-2"
          />
          <p v-if="fileError" class="text-red-500 mt-2">{{ fileError }}</p>
        </div>
      </template>
      <template #modalFooter>
        <button
            @click="modalAction.handler"
            class="px-4 py-2 bg-blue-500 text-white rounded-md"
        >
          {{ modalAction.btnName }}
        </button>
        <button
            @click="closeModal"
            class="ml-2 px-4 py-2 bg-gray-300 rounded-md"
        >
          Cancel
        </button>
      </template>
    </DefaultModal>
  </div>
</template>

<script setup>
import {ref, watch} from "vue";
import TreeTable from "primevue/treetable";
import Column from "primevue/column";
import CategoriesActions from "@/management/components/Categories/CategoriesActions.vue";
import {useCategoryManager} from "@/composables/useCategoriesManager.js";
import BaseTextInput from "@/components/Default/Inputs/BaseTextInput.vue";
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {CategoryService} from "@/services/Category/CategoryService.js";
import {useToastStore} from "@/stores/useToastStore.js";
import successCategorySettings from "@/management/components/Toasts/Category/successCategorySettings.js";
import failedCategorySettings from "@/management/components/Toasts/Category/failedCategorySettings.js";
import AddAttributeToCategory from "@/management/components/Categories/AddAttributeToCategory.vue";

const {
  nodes,
  isModalOpen,
  newCategory,
  modalAction,
  actionEvent,
  isFirstNode,
  categoryAttributes,
  updateNodeId,
  openModalToAddNewCategory,
  openModalToAddSubcategory,
  openModalToEditCategory,
  deleteNodeRecursively,
  initializeNodeDeletion,
  closeModal,
  loadCategories,
} = useCategoryManager();

const toast = useToastStore();

const attributes = ref([]);
const nodeToDelete = ref();
const fileError = ref(null);
const svgFile = ref(null);

const updateAttributes = (newAttributes) => {
  attributes.value = newAttributes;
};

const updateExistingAttributes = (updatedExistingAttributes) => {
  categoryAttributes.value = updatedExistingAttributes;
};

function handleFileInput(event) {
  const file = event.target.files[0];
  if (!file) return;

  if (file.type !== "image/svg+xml") {
    fileError.value = "Please select a valid SVG file.";
    svgFile.value = null;
  } else {
    fileError.value = null;
    svgFile.value = file;
  }
}

async function createCategory(payload) {
  await CategoryService.createCategory(payload, attributes.value)
      .then(async (response) => {
        if (response?.status === 201) {
          if (svgFile.value !== null) {
            await CategoryService.uploadCategoryIcon(
                svgFile.value,
                response?.data?.data?.id,
            ).catch((e) => {
              if (e.response?.status === 403) {
                toast.showToast(
                    e.response?.data?.message,
                    failedCategorySettings,
                );
              }
            });
          }

          updateNodeId(nodes.value, payload.key, response?.data?.data?.id);

          toast.showToast(response?.data?.message, successCategorySettings);
        }
        attributes.value = [];
      })
      .catch((e) => {
        if ([422, 400].includes(e.response?.status)) {
          toast.showToast(e.response?.data?.message, failedCategorySettings);
          deleteNodeRecursively(nodes.value, actionEvent.payload);
        } else {
          toast.showToast(
              "Unknown error. Contact support",
              failedCategorySettings,
          );
        }
        attributes.value = [];
      });
}

async function editCategory(payload) {
  await CategoryService.editCategory(payload, attributes.value)
      .then(async (response) => {
        if (response.status === 200) {
          if (svgFile.value !== null) {
            await CategoryService.editCategoryIcon(
                svgFile.value,
                payload?.id,
            ).catch((e) => {
              console.log(e);
            });
          }

          toast.showToast(response?.data?.message, successCategorySettings);
        }
        attributes.value = [];
      })
      .catch((e) => {
        toast.showToast(e.response?.data?.message, failedCategorySettings);
        attributes.value = [];
      });
}

async function deleteCategory(payload) {
  await CategoryService.deleteCategory(payload.id)
      .then((response) => {
        if (response.status === 200) {
          deleteNodeRecursively(nodes.value, payload);
          toast.showToast(response?.data?.message, successCategorySettings);
        }
      })
      .catch((e) => {
        if (e.response?.status === 409 && e.response?.data?.message) {
          console.log(payload);
          toast.showToast(e.response.data.message, failedCategorySettings);
        }
      });
}

watch(
    () => actionEvent,
    async (newType) => {
      if (newType.type === "delete") {
        await deleteCategory(actionEvent.payload);
      } else if (newType.type === "add") {
        await createCategory(actionEvent.payload);
      } else if (newType.type === "edit") {
        await editCategory(actionEvent.payload);
      }
    },
    {deep: true},
);

await loadCategories();
</script>
