<script setup>
import {onMounted, reactive, ref} from "vue";
import {ProductService} from "@/services/Product/ProductService.js";
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import ProductsSkeleton from "@/management/components/Product/Other/ProductsSkeleton.vue";
import ProductCard from "@/management/components/Product/Other/ProductCard.vue";

const categoriesWithProducts = reactive([]);

const isProductsFetched = ref(false);

async function getCategoriesProducts() {
  isProductsFetched.value = false;

  await ProductService.getProductsForEachRootCategory()
      .then((response) => {
        const data = response.data.data;
        const included = response.data.included;

        const productMap = {};

        included?.forEach((product) => {
          productMap[product.id] = product.attributes;
        });

        categoriesWithProducts.splice(
            0,
            categoriesWithProducts.length,
            ...data.map((category) => {
              const productIds = category.relationships.products.data.map(
                  (prod) => prod.id,
              );
              return {
                id: category.id,
                name: category.attributes.name,
                slug: category.attributes.slug,
                products: included ? productIds.map((id) => productMap[id]) : [],
                total: category.meta.total,
                next: 2,
              };
            }),
        );
      })
      .catch((e) => {
      })
      .finally(() => {
        isProductsFetched.value = true;
      });
}

onMounted(async () => {
  await getCategoriesProducts();
});
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <div>
        <h1 class="font-semibold text-4xl">Products</h1>
      </div>

      <products-skeleton :card-number="6" v-if="!isProductsFetched"/>

      <product-card v-else :products-categories="categoriesWithProducts"/>
    </section>
  </default-container>
</template>

<style scoped></style>
