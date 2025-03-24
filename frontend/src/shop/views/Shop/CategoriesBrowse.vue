<script setup>
import CategoryBrowseChildren from "@/shop/components/Categories/CategoryBrowseChildren.vue";
import {useRoute} from "vue-router";
import ProductsByCategory from "@/shop/components/Products/Shop/ProductsByCategory.vue";
import {reactive} from "vue";

const route = useRoute();

const selectedCategory = reactive({});

const handleCategorySelected = (category) => {
  Object.assign(selectedCategory, category);
};
</script>

<template>
  <main class="container mx-auto my-10 space-y-10">
    <category-browse-children
        :categorySlug="route.params.categorySlug"
        :initCategoryName="route.query?.name"
        @category-selected="handleCategorySelected"
    />
    <products-by-category :selectedCategory="selectedCategory"/>
  </main>
</template>

<style scoped>

</style>