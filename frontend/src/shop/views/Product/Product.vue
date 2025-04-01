<template>
  <main class="space-y-32 last-of-type:mb-20">
    <suspense>
      <product-info
          :product-id="route.params.productId"
          :product-slug="route.params.productSlug"
          @product-data="handleProductDataRetrieve"
      />
      <template #fallback>
        <suspense-loader/>
      </template>
    </suspense>
    <suspense v-if="productData?.category">
      <related-products :category-slug="productData?.category?.slug"/>
      <template #fallback>
        <suspense-loader/>
      </template>
    </suspense>
  </main>
</template>

<script setup>
import ProductInfo from "@/shop/components/Products/Shop/ProductInfo.vue";
import {useRoute} from "vue-router";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import RelatedProducts from "@/shop/components/Products/Sets/RelatedProducts.vue";
import {reactive} from "vue";

const route = useRoute();

const productData = reactive({});

const handleProductDataRetrieve = (product) => {
  Object.assign(productData, product);
};
</script>

<style scoped>
.wishlist svg {
  stroke: currentColor;
}

.wishlist.active svg {
  fill: #db4444;
  stroke: #db4444;
}
</style>
