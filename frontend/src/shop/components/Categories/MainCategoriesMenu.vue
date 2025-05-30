<script setup>
import {onMounted, ref} from "vue";
import {MegaMenu} from "primevue";
import {CategoryService} from "@/services/Category/CategoryService.js";

const items = ref([]);

onMounted(async () => {
  await CategoryService.getCategoriesTree(9)
      .then((response) => {
        items.value = transformCategories(response?.data.data);
      });
});

const transformCategories = (categories) => {
  let categoriesMapped = categories.map(category => ({
    label: category.name,
    route: `/shop/categories/${category.slug}`,
    items: category.children.length > 0
        ? [category.children.map(subcategory => ({
          label: subcategory.name,
          route: `/shop/categories/${subcategory.slug}`,
          items: subcategory.children.length > 0
              ? subcategory.children.map(child => ({
                label: child.name,
                route: `/shop/categories/${child.slug}`
              }))
              : []
        }))]
        : []
  }));

  categoriesMapped.push({
    label: "See more categories",
    route: "/shop/categories",
    items: []
  })

  return categoriesMapped;
};

</script>

<template>
  <MegaMenu :model="items" orientation="vertical">
    <template #item="{ item }">
      <router-link v-slot="{ href, navigate }" :to="item.route" custom>
        <a :href="href" @click="navigate" class="flex items-center justify-between group">
          <span class="ml-2 group-hover:underline underline-offset-4">{{ item.label }}</span>
          <i class="pi pi-angle-right"></i>
        </a>
      </router-link>
    </template>
  </MegaMenu>
</template>

<style scoped>

</style>