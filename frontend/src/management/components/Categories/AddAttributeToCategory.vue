<script setup>
import {computed, ref} from "vue";
import BaseTextInput from "@/components/Default/Inputs/BaseTextInput.vue";
import BaseSelectOption from "@/components/Default/Inputs/BaseSelectOption.vue";
import Button from "primevue/button";
import {CategoryService} from "@/services/Category/CategoryService.js";
import {useToastStore} from "@/stores/useToastStore.js";
import failedCategorySettings from "@/management/components/Toasts/Category/failedCategorySettings.js";
import successCategorySettings from "@/management/components/Toasts/Category/successCategorySettings.js";
import {types} from "@/utils/attributeTypes.js";

const props = defineProps({
  existingAttributes: {
    type: Array,
    default: () => [],
  },
  newAttributes: {
    type: Array,
    default: () => [],
  },
  categoryId: {
    type: Number,
    default: () => null,
  },
});

const emit = defineEmits([
  "update-new-attributes",
  "update-existing-attribute",
]);

const name = ref("");
const type = ref(null);
const required = ref(false);

const addAttribute = () => {
  if (name.value && type.value !== null) {
    const newAttribute = {
      id: `temp-${Date.now()}`,
      name: name.value,
      type: type.value,
      required: required.value,
    };

    const isDuplicate = [
      ...props.existingAttributes,
      ...props.newAttributes,
    ].some(
        (attr) =>
            attr.name.toLowerCase() === newAttribute.name.toLowerCase() &&
            attr.type === newAttribute.type,
    );

    if (!isDuplicate) {
      emit("update-new-attributes", [...props.newAttributes, newAttribute]);
    }

    name.value = "";
    type.value = null;
    required.value = false;
  }
};

const combinedAttributes = computed(() => [
  ...props.existingAttributes.map((attr) => ({
    ...attr,
    isNew: false,
    typeLabel:
        types.find((t) => t.value === attr.type)?.label.toLowerCase() ||
        attr.type.name,
  })),
  ...props.newAttributes.map((attr) => ({
    ...attr,
    isNew: true,
    typeLabel:
        types.find((t) => t.value === attr.type)?.label.toLowerCase() ||
        attr.type.name,
  })),
]);

async function deleteAttribute(attribute) {
  const toast = useToastStore();
  if (typeof attribute.id === "string" && attribute.id.startsWith("temp-")) {
    removeAttribute(attribute);
  } else {
    await CategoryService.deleteCategoryAttribute(
        props.categoryId,
        attribute.id,
    )
        .then((response) => {
          if (response.status === 200) {
            toast.showToast(response.data?.message, successCategorySettings);
            updateExistingAttributes(attribute);
          }
        })
        .catch((e) => {
          if (e.response?.status === 404 && e.response?.data?.message) {
            toast.showToast(e.response.data.message, failedCategorySettings);
            removeAttribute(attribute);
          }

          if (e.response?.status === 409) {
            toast.showToast(e.response.data.message, failedCategorySettings);
          }
        });
  }
}

function removeAttribute(attribute) {
  emit(
      "update-new-attributes",
      props.newAttributes.filter((attr) => attr.id !== attribute.id),
  );
}

function updateExistingAttributes(attribute) {
  emit(
      "update-existing-attribute",
      props.existingAttributes.filter((attr) => attr.id !== attribute.id),
  );
}
</script>

<template>
  <div class="space-y-4">
    <p class="text-sm text-gray-600">
      <span class="font-semibold">Attention!</span>
      This attributes will automatically apply to child categories.
    </p>
    <div class="flex gap-x-4 items-center">
      <base-text-input
          v-model="name"
          id="name"
          name="name"
          body-class="!px-2 !py-1"
          placeholder="Name"
          required
      />
      <base-select-option
          v-model="type"
          id="type"
          name="type"
          :options="types"
          class="w-full"
          body-class="!p-1 bg-gray-100"
          placeholder="Select type"
          required
      />
      <div class="flex items-center">
        <input
            id="checked-checkbox"
            type="checkbox"
            v-model="required"
            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500"
        />
        <label
            for="checked-checkbox"
            class="ms-2 text-sm font-medium text-gray-700"
        >Required</label
        >
      </div>
      <button
          @click="addAttribute"
          class="h-10 px-1 bg-blue-500 rounded-md text-white w-full"
      >
        Add Attribute
      </button>
    </div>

    <div class="relative overflow-x-auto">
      <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3">Attribute Name</th>
          <th scope="col" class="px-6 py-3">Type</th>
          <th scope="col" class="px-6 py-3">Required</th>
          <th scope="col" class="px-6 py-3"></th>
        </tr>
        </thead>
        <tbody>
        <tr
            v-for="(attribute, index) in combinedAttributes"
            :key="index"
            class="bg-white border-b"
        >
          <td class="px-6 py-4">{{ attribute.name }}</td>
          <td class="px-6 py-4">{{ attribute.typeLabel }}</td>
          <td class="px-6 py-4">{{ attribute.required ? "Yes" : "No" }}</td>
          <td class="px-6 py-4">
            <button
                v-if="attribute.category_id === categoryId || attribute.isNew"
                @click="deleteAttribute(attribute)"
                class="text-red-500"
            >
              Delete
            </button>
            <div v-else>
              <Button
                  v-tooltip="{
                    value:
                      'This attribute can only be deleted from the category in which it was created.',
                  }"
              >Read-only
              </Button>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped></style>
