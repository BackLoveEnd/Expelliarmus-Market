<script setup>
import { onMounted, ref } from 'vue'
import { CategoryService } from '@/services/CategoryService.js'
import { MegaMenu } from 'primevue'
import 'primeicons/primeicons.css'

const items = ref([])

onMounted(async () => {
  await CategoryService.getCategoriesTree()
      .then((response) => {
        items.value = transformCategories(response?.data.data)
      })
})

const transformCategories = (categories) => {
  return categories.map(category => ({
    label: category.name,
    route: `/products/categories/${category.slug}`,
    items: category.children.length > 0
        ? [category.children.map(subcategory => ({
          label: subcategory.name,
          route: `/products/categories/${subcategory.slug}`,
          items: subcategory.children.length > 0
              ? subcategory.children.map(child => ({
                label: child.name,
                route: `/products/categories/${child.slug}`
              }))
              : []
        }))]
        : []
  }))
}

</script>

<template>
  <MegaMenu
      :model="items"
      orientation="vertical"
      scroll-height="25rem"
      class="max-h-[25rem]"
  >
    <template #item="{ item }">
      <router-link v-slot="{ href, navigate }" :to="item.route" custom>
        <a :href="href" @click="navigate">
          <span :class="item.icon"/>
          <span class="ml-2 text-lg">{{ item.label }}</span>
          <i v-if="Array.isArray(item.items) && item.items.length > 0"
             class="pi pi-angle-right text-gray-600 text-base"></i>
        </a>
      </router-link>
    </template>
  </MegaMenu>
</template>

<style scoped>

</style>