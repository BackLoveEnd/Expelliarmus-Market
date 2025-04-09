<script setup>
import ProductDiscountCard from "@/components/Card/ProductDiscountCard.vue";
import ProductCard from "@/components/Card/ProductCard.vue";
import {ref, watch} from "vue";
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const props = defineProps({
  selectedFilters: Array,
  brandId: String
});

const products = ref([]);

const filtersMapped = ref({});

const isLoading = ref(true);

const nextPage = ref();

async function getProducts(page = 1) {
  const params = {
    filter: {
      brand: props.brandId,
      ...filtersMapped.value
    },
    page: page
  };

  isLoading.value = true;

  await ProductsShopService.getProductsShopCard(params)
      .then((response) => {
        if (page === 1) {
          products.value.splice(0, products.length, ...response?.data?.data?.map((product) => ({
            id: product?.id,
            title: product?.attributes?.title,
            image: product?.attributes?.image,
            slug: product?.attributes?.slug,
            price: product?.attributes?.price,
            discount: product?.attributes?.discount
          })));
        } else {
          products.value.push(...response?.data?.data?.map((product) => ({
            id: product?.id,
            title: product?.attributes?.title,
            image: product?.attributes?.image,
            slug: product?.attributes?.slug,
            price: product?.attributes?.price,
            discount: product?.attributes?.discount
          })));
        }

        nextPage.value = response?.data?.links.next_page;
      })
      .catch(e => {
      })
      .finally(() => isLoading.value = false);
}

function unsetProducts() {
  products.value = [];

  nextPage.value = null;
}

async function getNextPage() {
  await getProducts(nextPage.value);
}

watch(
    () => props.selectedFilters,
    async (newFilters) => {
      if (newFilters.length) {
        filtersMapped.value = newFilters.reduce((acc, filter) => {
          acc[filter.name] = Array.isArray(filter.value)
              ? filter.value.join(",")
              : filter.value;
          return acc;
        }, {});

        unsetProductsData();
        await getProducts();
      } else {
        filtersMapped.value = {};
        unsetProductsData();
        await getProducts();
      }
    },
    {deep: true, immediate: true}
);

function unsetProductsData() {
  products.value = [];
  nextPage.value = null;
}

watch(() => props.brandId, async (newBrand) => {
  if (newBrand) {
    unsetProducts();

    await getProducts();
  }
}, {deep: true});
</script>

<template>
  <div class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4" v-if="products.length > 0">
    <template v-for="product in products" :key="product.id">
      <product-discount-card :discounted-product="product" v-if="product.discount"/>
      <product-card :product="product" v-else/>
    </template>
  </div>
  <div v-else-if="products.length === 0 && !isLoading"
       class="flex flex-col items-center justify-center text-center h-96">
    <h1 class="mt-4 text-5xl font-semibold tracking-tight text-gray-900 dark:text-white">
      Products not found
    </h1>
  </div>
  <button
      v-if="nextPage !== null"
      class="mt-6 text-base font-medium text-gray-900 dark:text-white underline transition hover:no-underline"
      @click="getNextPage"
  >
    Browse More
  </button>
  <suspense-loader v-if="isLoading"/>
</template>

<style scoped>

</style>