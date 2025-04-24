<script setup>
import { onMounted, ref } from 'vue'
import { CategoryService } from '@/services/Category/CategoryService.js'
import TieredMenu from 'primevue/tieredmenu'

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
  <TieredMenu :model="items">
    <!--
          <template #item="{ item }">
            <router-link
                v-if="item.route"
                v-slot="{ href, navigate }"
                :to="item.route"
                custom
            >
              <a
                  :href="href"
                  @click="navigate"
                  class="flex items-center justify-between px-4 py-2 hover:bg-gray-100 transition"
              >
                <div class="flex items-center">
                  <span v-if="item.icon" :class="item.icon"/>
                  <span class="ml-2">{{ item.label }}</span>
                </div>
                &lt;!&ndash;            <i
                                v-if="Array.isArray(item.items) && item.items.length > 0"
                                class="pi pi-angle-right text-sm text-gray-600"
                            ></i>&ndash;&gt;
              </a>
            </router-link>

            <a
                v-else
                class="flex items-center justify-between px-4 py-2 hover:bg-gray-100 transition"
            >
              <div class="flex items-center">
                <span v-if="item.icon" :class="item.icon"/>
                <span class="ml-2">{{ item.label }}</span>
              </div>
            </a>
          </template>
    -->
  </TieredMenu>
</template>


<style scoped>

</style>