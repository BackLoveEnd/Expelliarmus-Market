<script setup>
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import ProductByBrandFilter from "@/shop/components/Products/Shop/Filters/ProductByBrandFilter.vue";
import ProductsByBrandFetcher from "@/shop/components/Products/Shop/Filters/ProductsByBrandFetcher.vue";
import {ref} from "vue";

const props = defineProps({
  brandSlug: String,
  brandId: Number | String
});

const selectedFilters = ref([]);

const handleSelectedFilters = (filters) => {
  selectedFilters.value = filters.value;
};

const productsExists = ref(true);
</script>

<template>
  <section class="antialiased">
    <div class="mx-auto container px-4 2xl:px-0">
      <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4" v-if="productsExists">
        <product-by-brand-filter
            :brand-slug="props.brandSlug"
            @categories-not-found="productsExists = false"
            @selected-filters="handleSelectedFilters"
        />
        <div class="lg:col-span-3">
          <suspense>
            <products-by-brand-fetcher :selected-filters="selectedFilters" :brand-id="brandId"/>
            <template #fallback>
              <suspense-loader/>
            </template>
          </suspense>
        </div>
      </div>
      <section v-else>
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
          <div class="mx-auto max-w-screen-sm text-center">
            <p
                class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white"
            >
              Products for this brand not found
            </p>
          </div>
        </div>
      </section>
    </div>
  </section>
</template>

<style scoped>

</style>