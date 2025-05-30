<script setup>
import SectionTitle from "@/components/Default/SectionTitle.vue";
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";
import ProductCard from "@/components/Card/ProductCard.vue";
import {ref} from "vue";
import ProductDiscountCard from "@/components/Card/ProductDiscountCard.vue";

const props = defineProps({
  categorySlug: String
});

const products = ref({
  id: null,
  title: null,
  image: null,
  slug: null,
  price: null,
  discount: null
});

async function getRelatedProducts() {
  await ProductsShopService.getRelatedProduct(props.categorySlug)
      .then((response) => {
        products.value = response?.data?.data?.map((product) => ({
          id: product?.id,
          title: product?.attributes?.title,
          image: product?.attributes?.image,
          slug: product?.attributes?.slug,
          price: product?.attributes?.price,
          discount: product?.attributes?.discount
        }));
      })
      .catch((e) => {

      });
}

await getRelatedProducts();
</script>

<template>
  <section class="container mx-auto mt-32 max-w-screen-2xl">
    <section-title title="Related Item"></section-title>
    <div class="grid grid-cols-5 gap-11" v-if="products.length">
      <template v-for="(product, index) in products" :key="index">
        <product-discount-card :discounted-product="product" v-if="product.discount !== null"/>

        <product-card :product="product" v-else/>
      </template>
    </div>
  </section>
</template>

<style scoped>

</style>