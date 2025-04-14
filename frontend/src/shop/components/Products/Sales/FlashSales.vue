<script setup>
import {ref, watch} from "vue";
import CardSlider from "@/components/Card/CardSlider.vue";
import ProductDiscountCard from "@/components/Card/ProductDiscountCard.vue";
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";
import SectionTitle from "@/components/Default/SectionTitle.vue";

const discountedProducts = ref([]);

const limit = ref(15);
const offset = ref(0);
const itemsToShow = 5;
const totalPages = ref(0);
const currentIndex = ref(0);
const isLoading = ref(false);
const totalItems = ref(0);

async function fetchProducts() {
  isLoading.value = true;

  try {
    const response = await ProductsShopService.getFlashSales(limit.value, offset.value);
    if (response?.data?.data) {
      const newProducts = response.data.data.map((discountedProducts) => ({
        id: discountedProducts.attributes.id,
        title: discountedProducts.attributes.title,
        image: discountedProducts.attributes.image,
        slug: discountedProducts.attributes.slug,
        discount: discountedProducts.attributes.discount,
      }));

      discountedProducts.value.push(...newProducts);

      totalItems.value = response.data?.meta?.total;

      totalPages.value = Math.ceil(discountedProducts.value.length / itemsToShow);
    }
  } catch (error) {
  } finally {
    isLoading.value = false;
  }
}

watch(currentIndex, async (newIndex) => {
  if (newIndex >= totalPages.value - 1) {
    offset.value = discountedProducts.value.length;

    limit.value += itemsToShow;

    await fetchProducts();
  }
});

await fetchProducts();
</script>

<template>
  <div v-if="discountedProducts.length > 0">
    <section-title :title="'Today`s'"/>
    <div class="space-y-14">
      <card-slider
          title="Flash Sales"
          v-model="currentIndex"
          :items-to-show="itemsToShow"
          :width-between-items="316"
          :items-length="totalItems"
          additional-classes="gap-10"
      >
        <product-discount-card
            v-for="discount in discountedProducts"
            :key="discount.id"
            :discounted-product="discount"
            class="product-card"
        />
      </card-slider>

      <div class="flex justify-center mt-4">
        <router-link
            to="#"
            class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
        >
          View All Products
        </router-link>
      </div>
      <div class="h-px bg-gray-300 border-0"></div>
    </div>
  </div>
</template>
