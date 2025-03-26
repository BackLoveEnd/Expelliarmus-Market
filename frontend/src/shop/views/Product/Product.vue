<template>
  <main class="space-y-32 last-of-type:mb-20">
    <suspense>
      <product-info :product-id="route.params.productId" :product-slug="route.params.productSlug"/>
      <template #fallback>
        <suspense-loader/>
      </template>
    </suspense>
    <section class="container mx-auto mt-32 max-w-screen-2xl">
      <section-title title="Related Item"></section-title>
      <div class="grid grid-cols-5 gap-11">
        <!--        <product-card/>
                <product-card/>
                <product-card/>
                <product-card/>
                <product-card/>-->
      </div>
    </section>
  </main>
</template>

<script setup>
import SectionTitle from "@/components/Default/SectionTitle.vue";
import ProductInfo from "@/shop/components/Products/Shop/ProductInfo.vue";
import {useRoute} from "vue-router";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const route = useRoute();
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
