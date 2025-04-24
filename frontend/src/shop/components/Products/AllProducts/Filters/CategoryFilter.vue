<script setup>
import {CategoriesShopService} from "@/services/Category/CategoriesShopService.js";
import {ref} from "vue";

const props = defineProps({
  filterSection: Object,
  sectionIndex: Number
});

const subCategories = ref([]);

const emit = defineEmits(["category-selected", "sub-categories"]);

async function getSubCategories(categoryName, categorySlug) {
  if (subCategories.value.findIndex((sub) => sub.name === categoryName) !== -1) return;

  try {
    const response = await CategoriesShopService.getChildrenForCategory(categorySlug);
    subCategories.value.push({
      name: categoryName,
      slug: categorySlug,
      items: response?.data?.data?.map(sub => ({
        id: sub.id,
        name: sub.attributes.name,
        slug: sub.attributes.slug,
      }))
    });

    emit("sub-categories", subCategories);
  } catch (error) {
    if (error?.status === 404) {

    }
  }
}

async function toggleCategory(category) {
  category.checked = !category.checked;

  if (category.checked) {
    await getSubCategories(category.label, category.slug);
  } else {
    subCategories.value = subCategories.value.filter(sub => sub.slug !== category.slug);
  }

  emit("category-selected", category);
}
</script>

<template>
  <div v-for="(item, itemIdx) in filterSection.items" :key="item.id" class="flex gap-3">
    <div class="flex h-5 shrink-0 items-center">
      <div class="group grid size-4 grid-cols-1">
        <input :id="`filter-${sectionIndex}-${itemIdx}`" :value="item.id"
               type="checkbox" :checked="item.checked"
               @change="toggleCategory(item)"
               class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 indeterminate:border-indigo-600 indeterminate:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100"/>
        <svg
            class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-disabled:stroke-gray-950/25"
            viewBox="0 0 14 14" fill="none">
          <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"/>
          <path class="opacity-0 group-has-indeterminate:opacity-100" d="M3 7H11" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>
    <label :for="`filter-${sectionIndex}-${itemIdx}`" class="text-sm text-gray-600">{{ item.label }}</label>
  </div>
</template>

<style scoped>

</style>