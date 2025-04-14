<script setup>
import SectionTitle from "@/components/Default/SectionTitle.vue";
import {computed, ref} from "vue";
import {useScrolling} from "@/composables/useScrolling.js";
import {BrandsService} from "@/services/Brand/BrandsService.js";
import BrandBrowseCard from "@/shop/components/Brands/BrandBrowseCard.vue";

const brands = ref([]);

const brandsCollapsed = computed(() => {
  return brands.value.map((brand) => ({...brand.attributes, id: brand.id}));
});

async function fetchBrands() {
  await BrandsService.fetchBrands('/shop/brands/browse-list?limit=7')
      .then((response) => {
        brands.value = response?.data?.data ?? [];
      })
      .catch((error) => {

      });
}

await fetchBrands();
</script>

<template>
  <div v-if="brandsCollapsed.length">
    <section-title :title="'Brands'"/>
    <div class="space-y-16">
      <div class="flex justify-between">
        <brand-browse-card
            v-for="(brand, index) in brandsCollapsed"
            :key="index"
            :brand="{
              id: brand.id,
              slug: brand.slug,
              name: brand.brand_name,
              logo: brand.logo
            }"
        />
      </div>
      <div class="flex justify-center">
        <router-link
            @click.prevent="useScrolling().scrollToTop()"
            :to="{ name: 'brands-overview' }"
            class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
        >
          Show More
        </router-link>
      </div>
      <div class="h-px bg-gray-300 border-0"></div>
    </div>
  </div>
</template>

<style scoped>

</style>