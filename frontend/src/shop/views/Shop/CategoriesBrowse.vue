<script setup>
import CategoryBrowseChildren from "@/shop/components/Categories/CategoryBrowseChildren.vue";
import {useRoute} from "vue-router";
import ProductsByCategory from "@/shop/components/Products/Shop/ProductsByCategory.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import {ref} from "vue";

const route = useRoute();

const selectedCategory = ref({});

const handleCategorySelected = (category) => {
  selectedCategory.value = category;
};
</script>

<template>
  <main class="container mx-auto my-10 space-y-10">
    <suspense>
      <category-browse-children :categorySlug="route.params.categorySlug" @category-selected="handleCategorySelected"/>
      <template #fallback>
        <suspense-loader/>
      </template>
    </suspense>
    <products-by-category :selectedCategory="selectedCategory"/>
  </main>
</template>

<style scoped>

</style>