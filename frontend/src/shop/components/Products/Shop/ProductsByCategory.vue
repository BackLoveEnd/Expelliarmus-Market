<script setup>
import {ProductsShopService} from "@/services/ProductsShopService.js";
import {ref, watch} from "vue";
import ProductCard from "@/components/Card/ProductCard.vue";
import ProductDiscountCard from "@/components/Card/ProductDiscountCard.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const props = defineProps({
  selectedCategory: Object
});

const products = ref([]);

const isLoading = ref(true);

const nextPage = ref();

async function getProducts(page = 1) {
  const params = {
    filter: {
      category: props.selectedCategory.slug
    },
    page: page
  };

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

async function getNextPage() {
  await getProducts(nextPage.value);
}

watch(() => props.selectedCategory, async (newCategory) => {
  if (newCategory?.slug) {
    products.value = [];
    isLoading.value = true;

    await getProducts();
  }
}, {immediate: true, deep: true});
</script>

<template>
  <section class="antialiased" v-if="!isLoading">
    <div class="mx-auto container px-4 2xl:px-0">
      <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
        <div>
          <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">{{
              selectedCategory.name
            }}</h2>
        </div>
        <div class="flex items-center space-x-4">
          <button data-modal-toggle="filterModal" data-modal-target="filterModal" type="button"
                  class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto">
            <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                 height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-width="2"
                    d="M18.796 4H5.204a1 1 0 0 0-.753 1.659l5.302 6.058a1 1 0 0 1 .247.659v4.874a.5.5 0 0 0 .2.4l3 2.25a.5.5 0 0 0 .8-.4v-7.124a1 1 0 0 1 .247-.659l5.302-6.059c.566-.646.106-1.658-.753-1.658Z"/>
            </svg>
            Filters
            <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                 height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 9-7 7-7-7"/>
            </svg>
          </button>
          <button id="sortDropdownButton1" data-dropdown-toggle="dropdownSort1" type="button"
                  class="flex w-full items-center justify-center rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700 sm:w-auto">
            <svg class="-ms-0.5 me-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                 height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 4v16M7 4l3 3M7 4 4 7m9-3h6l-6 6h6m-6.5 10 3.5-7 3.5 7M14 18h4"/>
            </svg>
            Sort
            <svg class="-me-0.5 ms-2 h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                 height="24" fill="none" viewBox="0 0 24 24">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="m19 9-7 7-7-7"/>
            </svg>
          </button>
          <div id="dropdownSort1"
               class="z-50 hidden w-40 divide-y divide-gray-100 rounded-lg bg-white shadow dark:bg-gray-700"
               data-popper-placement="bottom">
            <ul class="p-2 text-left text-sm font-medium text-gray-500 dark:text-gray-400"
                aria-labelledby="sortDropdownButton">
              <li>
                <a href="#"
                   class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                  The most popular </a>
              </li>
              <li>
                <a href="#"
                   class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                  Newest </a>
              </li>
              <li>
                <a href="#"
                   class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                  Increasing price </a>
              </li>
              <li>
                <a href="#"
                   class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                  Decreasing price </a>
              </li>
              <li>
                <a href="#"
                   class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                  No. reviews </a>
              </li>
              <li>
                <a href="#"
                   class="group inline-flex w-full items-center rounded-md px-3 py-2 text-sm text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-600 dark:hover:text-white">
                  Discount % </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div v-if="products.length">
        <div class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">
          <template v-for="product in products" :key="product.id">
            <product-discount-card :discounted-product="product" v-if="product.discount"/>
            <product-card :product="product" v-else/>
          </template>
        </div>
        <div class="w-full text-center" v-if="nextPage">
          <button
              @click="getNextPage"
              type="button"
              class="rounded-lg border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
            Show more
          </button>
        </div>
      </div>
      <div v-else class="flex flex-col items-center justify-center space-y-4 py-10">
        <i class="pi pi-times-circle text-gray-400 dark:text-gray-600 text-6xl"></i>
        <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">No products found</p>
        <p class="text-gray-500 dark:text-gray-400 text-sm text-center max-w-md">
          Unfortunately, there are no products available in this category. Try selecting a different category or check
          back later.
        </p>
        <router-link
            to="/"
            class="mt-4 inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-white hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800"
        >
          <i class="pi pi-arrow-left text-white mr-2"></i>
          Back to Home
        </router-link>
      </div>
    </div>
  </section>
  <suspense-loader v-else/>
</template>

<style scoped>

</style>