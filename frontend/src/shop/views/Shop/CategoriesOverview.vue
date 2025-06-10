<script setup>
import {CategoriesShopService} from "@/services/Category/CategoriesShopService.js";
import {onMounted, ref} from "vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const categories = ref([]);

const isLoading = ref(true);

async function getCategories() {
  isLoading.value = true;

  await CategoriesShopService.getRootCategories()
      .then((response) => {
        categories.value = response?.data?.data?.map((category) => category.attributes);
      })
      .catch((e) => {
      })
      .finally(() => isLoading.value = false);
}

onMounted(async () => await getCategories());
</script>

<template>
  <main class="container mx-auto">
    <section class="py-8 antialiased md:py-16">
      <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
        <div class="mb-4 flex items-center justify-between gap-4 md:mb-8">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Shop by category</h2>
        </div>
        <suspense-loader v-if="isLoading"/>
        <div class="grid gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4" v-else>
          <router-link
              v-for="(category, index) in categories"
              :key="index"
              :to="{ name: 'categories-browse', params: { categorySlug: category.slug }, query: { name: category.name }}"
              class="flex items-center rounded-lg border border-gray-200 bg-white px-4 py-2 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-gray-700">
            <img :alt="category.name" :src="category.icon" class="w-4 h-4 me-2">
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ category.name }}</span>
          </router-link>
        </div>
      </div>
    </section>
  </main>
</template>

<style scoped>

</style>