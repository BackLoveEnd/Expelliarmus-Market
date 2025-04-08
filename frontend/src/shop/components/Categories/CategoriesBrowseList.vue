<script setup>
import SectionTitle from "@/components/Default/SectionTitle.vue";
import CategoryCard from "@/components/Card/CategoryCard.vue";
import {CategoriesShopService} from "@/services/Category/CategoriesShopService.js";
import {computed, ref} from "vue";
import {useScrolling} from "@/composables/useScrolling.js";

const categories = ref([]);

const totalItems = ref(0);

const categoriesCollapsed = computed(() => {
  return categories.value.map((category) => category.attributes);
});

async function fetchCategories() {
  await CategoriesShopService.getCategoriesBrowseList(7)
      .then((response) => {
        categories.value = response?.data?.data ?? [];

        totalItems.value = categories.value.length;
      })
      .catch((e) => {

      });
}

await fetchCategories();
</script>

<template>
  <div v-if="categoriesCollapsed.length">
    <section-title :title="'Categories'"/>
    <div class="space-y-16">
      <div class="flex justify-between">
        <category-card
            v-for="(category, index) in categoriesCollapsed"
            :key="index"
            :category-icon="category.icon"
            :category-name="category.name"
            :category-slug="category.slug"
        />
      </div>
      <div class="flex justify-center">
        <router-link
            @click.prevent="useScrolling().scrollToTop()"
            :to="{ name: 'categories-overview' }"
            class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
        >
          Show More
        </router-link>
      </div>
      <div class="h-px bg-gray-300 border-0"></div>
    </div>
  </div>
</template>

<style scoped>

</style>