<script setup>
import {ref} from "vue";
import ProductByCategoriesFilter from "@/shop/components/Products/Shop/Filters/ProductByCategoriesFilter.vue";
import ProductsByCategoryFetcher from "@/shop/components/Products/Shop/Filters/ProductsByCategoryFetcher.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const props = defineProps({
  selectedCategory: Object
});

const selectedFilters = ref([]);

const handleSelectedFilter = (filters) => {
  selectedFilters.value = filters.value;
};
</script>

<template>
  <section class="antialiased">
    <div class="mx-auto container px-4 2xl:px-0">
      <div class="mb-4 items-end justify-between space-y-4 sm:flex sm:space-y-0 md:mb-8">
        <div>
          <h2 class="mt-3 text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">{{
              selectedCategory.name
            }}</h2>
        </div>
      </div>
      <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4" v-if="selectedCategory?.slug">
        <product-by-categories-filter
            :category-slug="selectedCategory.slug"
            @selected-filters="handleSelectedFilter"
        />

        <div class="lg:col-span-3">
          <suspense>
            <products-by-category-fetcher :category-slug="selectedCategory.slug" :selected-filters="selectedFilters"/>
            <template #fallback>
              <suspense-loader/>
            </template>
          </suspense>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>

</style>