<script setup>
import ProductEditForm from "@/management/components/Product/Edit/ProductEditForm.vue";
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {reactive, ref} from "vue";
import {ProductService} from "@/services/Product/ProductService.js";
import {useToastStore} from "@/stores/useToastStore.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import {useRouter} from "vue-router";
import defaultWarningSettings from "@/components/Default/Toasts/Default/defaultWarningSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";

const props = defineProps({
  id: Number | String,
  slug: String,
});

const product = reactive({
  id: null,
  title: null,
  article: null,
  main_description_markdown: null,
  title_description: null,
  images: null,
  previewImage: null,
  createdAt: null,
  updatedAt: null,
  published: null,
  is_combined_attributes: null,
  specifications: null,
  categoryId: null,
  brandId: null,
});

const variations = ref([]);

const toast = useToastStore();

const router = useRouter();

let warehouse = reactive({
  total_quantity: null,
  price: null,
});

const isDeleteModalOpen = ref(false);

async function getProductStaff() {
  await ProductService.getProductStaffInfo(props.id)
      .then((response) => {
        Object.assign(product, response.product);

        variations.value = response.variations;

        warehouse = {
          total_quantity: response.warehouse.total_quantity,
          price: response.warehouse.default_price
        };
      })
      .catch((e) => {
        if (e.response?.status === 404) {
          router.push({name: "product-list"});
        }
      });
}

async function publish() {
  await ProductService.publishProduct(product.id)
      .then((response) => {
        if (response?.status === 200) {
          toast.showToast(response?.data?.message, defaultSuccessSettings);
          router.push({name: "product-list"});
        }
      })
      .catch((e) => {
        if (e.response?.status === 403) {
          toast.showToast(e.response?.data?.message, defaultWarningSettings);
        } else {
          toast.showToast("Unknown error. Please try again or contact us", defaultErrorSettings);
        }
      });
}

async function moveToTrash() {
  await ProductService.moveToTrash(product.id)
      .then((response) => {
        if (response?.status === 200) {
          toast.showToast(response?.data?.message, defaultSuccessSettings);
          router.push({name: "product-list"});
        }
      })
      .catch((e) => {
        if (e.response?.status === 403) {
          toast.showToast(e.response?.data?.message, defaultWarningSettings);
        } else if (e.response?.status > 500) {
          toast.showToast("Unknown error. Please try again or contact us.", defaultErrorSettings);
        }
        isDeleteModalOpen.value = false;
      });
}

await getProductStaff();
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <div class="space-y-4">
        <h1 class="text-3xl font-semibold">
          Product Edit - {{ product.title }}
        </h1>
        <div class="flex flex-col gap-y-1 text-gray-500 text-xs">
          <span>Created: {{ product.createdAt }}</span>
          <span>Last updated: {{ product.updatedAt }}</span>
        </div>
        <div class="flex gap-x-4">
          <button
              @click="isDeleteModalOpen = true"
              class="p-2 bg-red-400 hover:bg-red-600 rounded-md text-white text-sm">Move to Trash
          </button>
          <button
              v-if="! product.published"
              @click="publish"
              class="p-2 bg-green-400 hover:bg-green-600 rounded-md text-white text-sm">
            Publish
          </button>
        </div>
      </div>
      <main class="space-y-32">
        <ProductEditForm
            :product="product"
            :warehouse="warehouse"
            :variations="variations"
        />
      </main>
    </section>
  </default-container>
  <default-modal
      :is-active="isDeleteModalOpen"
      @close-modal="isDeleteModalOpen = false"
      max-width="max-w-sm"
      space-between-templates="space-y-4"
  >
    <template #modalHeading>
      <span class="font-semibold">Confirm moving product to trash?</span>
    </template>
    <template #modalBody>
      <span class="text-sm">Product - {{ product.title }} will be moved to trash and automatically unpublished.</span>
    </template>
    <template #modalFooter>
      <div class="space-x-4">
        <button
            @click="isDeleteModalOpen = false"
            type="button"
            class="p-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel
        </button>
        <button @click="moveToTrash" type="button" class="p-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">
          Confirm
        </button>
      </div>
    </template>
  </default-modal>
</template>

<style scoped>

</style>