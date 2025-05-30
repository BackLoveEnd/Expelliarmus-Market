<script setup>
import {WarehouseService} from "@/services/Product/WarehouseService.js";
import {computed, reactive, ref, watch} from "vue";
import {statusColors} from "@/utils/statusColors.js";
import WarehouseSingleVariationViewer from "@/management/components/Warehouse/WarehouseSingleVariationViewer.vue";
import WarehouseCombinedVariationsViewer from "@/management/components/Warehouse/WarehouseCombinedVariationsViewer.vue";

const props = defineProps({
  product: Number | String,
});

let product = reactive({
  id: null,
  title: null,
  article: null,
  previewImage: null,
  variationType: null,
  status: {
    name: null,
    colorType: null,
  },
});

const category = reactive({
  name: null,
});

const brand = reactive({
  brand_name: null,
});

const warehouse = reactive({
  price: null,
  quantity: null,
  arrived_at: null,
});

const variations = ref([]);

const statusColor = computed(() => {
  const status = statusColors.find(
      (item) => item.name === product.status.colorType,
  );

  return status?.color ?? "";
});

async function getWarehouseProduct() {
  try {
    const response = await WarehouseService.getProduct(props.product);

    Object.assign(product, response.product);
    Object.assign(warehouse, response.warehouse);
    Object.assign(category, response.category);
    Object.assign(brand, response.brand);

    variations.value = response.variations;
  } catch (e) {
  }
}

await getWarehouseProduct();

watch(
    () => props.product,
    async () => {
      await getWarehouseProduct();
    },
);
</script>

<template>
  <section class="container mx-auto w-3/4 space-y-16" v-if="product.id">
    <h2 class="text-center text-3xl">Product Information</h2>
    <div class="flex flex-row gap-x-12 justify-center">
      <div>
        <img
            :src="product.previewImage"
            alt="Product Image"
            class="max-w-[240px] h-auto"
        />
      </div>

      <div class="flex flex-col justify-between">
        <h2 class="text-2xl font-bold text-gray-900 sm:pr-12">
          Title: {{ product.title }}
        </h2>
        <div class="grid grid-cols-2 gap-4 flex-grow">
          <div class="space-y-2">
            <span class="text-lg font-semibold text-gray-900"> General </span>
            <p class="text-sm text-gray-900" :class="statusColor">
              Status: {{ product.status.name }}
            </p>
            <p class="text-sm text-gray-900">Article: {{ product.article }}</p>
            <p class="text-sm text-gray-900">
              Arrival time: {{ warehouse.arrived_at }}
            </p>
          </div>
          <div class="space-y-2">
            <span class="text-lg font-semibold">Warehouse</span>
            <p class="text-gray-900 text-sm">
              Price:
              {{
              parseInt(warehouse.price)
              ? "$" + warehouse.price
              : warehouse.price
              }}
            </p>
            <p class="text-gray-900 text-sm">
              Total quantity: {{ warehouse.quantity }} unit(s).
            </p>
          </div>
          <div class="space-y-2">
            <span class="text-lg font-semibold">Category</span>
            <p class="text-gray-900 text-sm">Category: {{ category.name }}</p>
          </div>
          <div class="space-y-2">
            <span class="text-lg font-semibold">Brand</span>
            <p class="text-gray-900 text-sm">Brand: {{ brand.brand_name }}</p>
          </div>
        </div>
      </div>
    </div>
    <section
        class="container mx-auto space-y-8"
        v-if="product.variationType !== null"
    >
      <h2 class="text-center text-3xl">Product Options</h2>
      <warehouse-combined-variations-viewer
          v-if="product.variationType === 'combined'"
          :variations="variations"
      />
      <warehouse-single-variation-viewer v-else :variations="variations"/>
    </section>
  </section>
</template>

<style scoped></style>
