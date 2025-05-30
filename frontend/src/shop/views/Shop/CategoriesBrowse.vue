<script setup>
import CategoryBrowseChildren from "@/shop/components/Categories/CategoryBrowseChildren.vue";
import {useRoute} from "vue-router";
import ProductsByCategory from "@/shop/components/Products/Shop/ProductsByCategory.vue";
import {onMounted, onUnmounted, reactive} from "vue";
import {useBreadCrumbStore} from "@/stores/useBreadCrumbStore.js";

const route = useRoute();

const selectedCategory = reactive({});

const breadCrumbStore = useBreadCrumbStore();

onMounted(() => {
  const savedBreadcrumbs = localStorage.getItem('breadcrumb');

  const breadcrumbsParsed = JSON.parse(savedBreadcrumbs);

  if (breadcrumbsParsed?.breadcrumbs?.length) {
    breadCrumbStore.setBreadcrumbs(breadcrumbsParsed.breadcrumbs);
  } else {
    breadCrumbStore.setBreadcrumbs([
      {name: 'Categories', url: "/shop/categories"}
    ]);
  }
});

onUnmounted(() => {
  breadCrumbStore.clearBreadcrumbs();

  localStorage.removeItem('breadcrumb');
});

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