<script setup>
import SectionTitle from "@/components/Default/SectionTitle.vue";
import ProductCard from "@/components/Card/ProductCard.vue";
import {computed, ref} from "vue";
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";
import {useScrolling} from "@/composables/useScrolling.js";

const products = ref([]);

const productsCollapsed = computed(() => {
  return products.value.map((product) => product.attributes);
});

async function getProducts() {
  await ProductsShopService.getExploredProducts()
      .then((response) => {
        products.value = response?.data?.data;
      })
      .catch((e) => {

      });
}

await getProducts();
</script>

<template>
  <div v-if="productsCollapsed.length > 0">
    <section-title :title="'Our Products'"/>
    <div class="flex justify-between mb-10">
      <p class="text-4xl font-semibold">Explore Our Products</p>
    </div>
    <div class="space-y-14">
      <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-11">
        <product-card v-for="(product, index) in productsCollapsed" :key="index" :product="product"/>
      </div>
      <div class="flex justify-center">
        <router-link
            @click.prevent="useScrolling().scrollToTop()"
            :to="{ name: 'all-products'}"
            class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
        >
          View More Products
        </router-link>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>