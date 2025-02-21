<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import ProductEditForm from "@/management/components/Product/Other/ProductEditForm.vue";
import { onMounted, reactive, ref } from "vue";
import { ProductService } from "@/services/ProductService.js";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

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
  is_combined_attributes: null,
  specifications: null,
  categoryId: null,
  brandId: null,
});

const variations = ref([]);

const warehouse = ref({
  total_quantity: null,
  default_price: null,
});

const isLoading = ref(true);

async function getProductStaff() {
  isLoading.value = true;

  await ProductService.getProductStaffInfo(props.id)
    .then((response) => {
      Object.assign(product, response.product);

      variations.value = response.variations;

      Object.assign(warehouse.value, response.warehouse.attributes);
    })
    .catch((e) => {})
    .finally(() => {
      isLoading.value = false;
    });
}

onMounted(() => {
  getProductStaff();
});
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <div class="space-y-2">
        <h1 class="text-3xl font-semibold">
          Product Edit - {{ product.title }}
        </h1>
        <div class="flex flex-col gap-y-1 text-gray-500 text-xs">
          <span class="">Created: {{ product.createdAt }}</span>
          <span>Last updated: {{ product.updatedAt }}</span>
        </div>
      </div>
      <main class="space-y-32">
        <ProductEditForm
          v-if="!isLoading"
          :product="product"
          :warehouse="warehouse"
          :variations="variations"
        />
        <suspense-loader v-else />
      </main>
    </section>
  </default-container>
</template>

<style scoped></style>
