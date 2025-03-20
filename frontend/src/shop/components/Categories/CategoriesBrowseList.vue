<script setup>
import SectionTitle from "@/components/Default/SectionTitle.vue";
import CardSlider from "@/components/Card/CardSlider.vue";
import CategoryCard from "@/components/Card/CategoryCard.vue";
import {CategoriesShopService} from "@/services/CategoriesShopService.js";
import {computed, ref} from "vue";

const categories = ref([]);

const totalItems = ref(0);

const categoriesCollapsed = computed(() => {
  return categories.value.map((category) => category.attributes);
});

async function fetchCategories() {
  await CategoriesShopService.getCategoriesBrowseList()
      .then((response) => {
        categories.value = response?.data?.data ?? [];

        totalItems.value = categories.value.length;
      });
}

await fetchCategories();
</script>

<template>
  <div v-if="categoriesCollapsed.length">
    <section-title :title="'Categories'"/>
    <div class="space-y-16">
      <card-slider
          :title="'Browse By Category'"
          :items-to-show="7"
          :width-between-items="224"
          additional-classes="gap-8"
          :items-length="totalItems"
      >
        <category-card
            v-for="(category, index) in categoriesCollapsed"
            :key="index"
            :category-icon="category.icon"
            :category-name="category.name"
            :link="'/shop/categories/' + category.slug"
        />
      </card-slider>
      <div class="h-px bg-gray-300 border-0"></div>
    </div>
  </div>
</template>

<style scoped>

</style>