<script setup>
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";
import ProductCard from "@/components/Card/ProductCard.vue";
import {ref, watch} from "vue";
import ProductDiscountCard from "@/components/Card/ProductDiscountCard.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const props = defineProps({
  filters: {
    type: Array,
    default: []
  },
  sort: {
    type: Object,
    default: () => ({field: 'price', direction: 'asc'})
  }
});

const filtersMapped = ref({});
const products = ref([]);
const totalProducts = ref(0);
const nextPage = ref(null);
const isLoading = ref(false);

async function getProducts(page = 1) {
  const params = {
    filter: filtersMapped.value,
    page: page,
  };

  console.log(params);

  isLoading.value = true;

  await ProductsShopService.getProductsShopCard(params)
      .then((response) => {
        if (page === 1) {
          products.value = response?.data?.data?.map((product) => ({
            id: product.id,
            title: product.attributes.title,
            image: product.attributes.image,
            slug: product.attributes.slug,
            price: product.attributes.price,
            discount: product.attributes.discount ?? null
          }));
        } else {
          products.value.push(...response?.data?.data?.map((product) => ({
            id: product.id,
            title: product.attributes.title,
            image: product.attributes.image,
            slug: product.attributes.slug,
            price: product.attributes.price,
            discount: product.attributes.discount ?? null
          })));
        }

        totalProducts.value = response?.data?.meta?.total;

        nextPage.value = response?.data?.links?.next_page;

        sortProducts();
      })
      .catch((e) => {
        if (e?.status === 404) {
          unsetProductsData();
        }
      })
      .finally(() => isLoading.value = false);
}

const browseMore = async () => {
  await getProducts(nextPage.value);
};

watch(
    () => props.filters,
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
    {deep: true}
);

watch(
    () => props.sort,
    async () => {
      sortProducts();
    },
    {deep: true}
);

function unsetProductsData() {
  products.value = [];
  nextPage.value = null;
  totalProducts.value = 0;
}

function sortProducts() {
  const {field, direction} = props.sort;

  if (field && products.value.length > 0) {
    products.value.sort((a, b) => {
      if (a[field] < b[field]) {
        return direction === 'asc' ? -1 : 1;
      }
      if (a[field] > b[field]) {
        return direction === 'asc' ? 1 : -1;
      }
      return 0;
    });
  }
}

await getProducts();
</script>

<template>
  <section class="flex flex-col items-center justify-center w-full">
    <div
        v-if="products.length > 0"
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 w-full px-4"
    >
      <template v-for="product in products" :key="product.id">
        <product-discount-card v-if="product.discount !== null" :discounted-product="product"/>
        <product-card v-else :product="product"/>
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
        @click="browseMore"
    >
      Browse More
    </button>

    <suspense-loader v-if="isLoading"/>
  </section>
</template>

<style scoped>

</style>