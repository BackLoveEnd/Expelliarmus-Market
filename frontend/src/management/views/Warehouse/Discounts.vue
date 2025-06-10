<script setup>
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import ListOfDiscountedProducts from "@/management/components/Discounts/ListOfDiscountedProducts.vue";
import ProductSearcherForDiscount from "@/management/components/Discounts/ProductSearcherForDiscount.vue";
import {ref} from "vue";
import DiscountsProductViewer from "@/management/components/Discounts/DiscountsProductViewer.vue";

const productId = ref(null);

const handleProductSelection = (id) => {
  productId.value = id;
};
</script>

<template>
  <default-container>
    <div class="space-y-20 my-14">
      <section class="container mx-auto flex flex-col">
        <div>
          <h1 class="font-semibold text-4xl">Product Discounts</h1>
        </div>
      </section>
      <section class="container mx-auto flex flex-col items-center">
        <product-searcher-for-discount
            @selected-product="handleProductSelection"
        />
      </section>
      <section class="container mx-auto" v-if="productId">
        <suspense>
          <discounts-product-viewer :product="productId"/>
          <template #fallback>
            <suspense-loader/>
          </template>
        </suspense>
      </section>
      <hr class="w-3/4 text-gray-400 mx-auto">
      <section class="container mx-auto flex flex-col">
        <main class="space-y-32 flex flex-grow items-center">
          <Suspense>
            <list-of-discounted-products/>
            <template #fallback>
              <suspense-loader/>
            </template>
          </Suspense>
        </main>
      </section>
    </div>
  </default-container>
</template>

<style scoped>

</style>