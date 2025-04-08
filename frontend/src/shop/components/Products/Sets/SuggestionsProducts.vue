<script setup>
import SectionTitle from "@/components/Default/SectionTitle.vue";
import {computed, ref} from "vue";
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";
import ProductCard from "@/components/Card/ProductCard.vue";

const products = ref([]);

const productsCollapsed = computed(() => {
  return products.value.map((product) => product.attributes);
});

async function getSuggestions() {
  await ProductsShopService.getSuggestions()
      .then((response) => {
        products.value = response?.data?.data;
      })
      .catch((e) => {

      });
}

await getSuggestions();
</script>

<template>
  <section class="container mx-auto space-y-16" v-if="productsCollapsed.length > 0">
    <div class="flex justify-between items-center">
      <section-title :title="'Just For You'"/>
      <router-link to="#"
                   class="px-12 py-4 border border-black flex items-center rounded-md justify-center hover:bg-[#db4444] hover:text-white hover:border-0">
        See All
      </router-link>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-11">
      <product-card v-for="(product, index) in productsCollapsed" :key="index" :product="product"/>
    </div>
  </section>
</template>

<style scoped>

</style>