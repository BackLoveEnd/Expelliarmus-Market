<script setup>
import { TreeSelect } from "primevue";
import api from "@/utils/api.js";
import { onMounted, reactive, ref, watch } from "vue";

let categories = ref([]);
let selectedCategoryId = ref(null);
let selectedCategory = reactive({});

const props = defineProps({
  modelValue: null,
  categoryId: Number,
});

onMounted(async () => {
  categories.value = await getCategories();

  if (props.categoryId) {
    const foundCategory = findCategoryById(props.categoryId, categories.value);
    if (foundCategory) {
      selectedCategoryId.value = { [foundCategory.key]: true };
    }
  }
});

async function getCategories() {
  try {
    const response = await api().get("/management/categories?include=children");

    return transformCategories(response.data.data);
  } catch (e) {
    return [];
  }
}

function transformCategories(data, parentKey = "") {
  return data.map((item, index) => {
    const currentKey = parentKey ? `${parentKey}-${index}` : String(index);
    return {
      key: currentKey,
      id: item.id,
      isLast: item.last,
      label: item.name,
      data: item.name,
      root: item.root,
      children:
        item.children && item.children.length > 0
          ? transformCategories(item.children, currentKey)
          : [],
    };
  });
}

function findCategoryByKey(key, categories) {
  for (let category of categories) {
    if (category.key === key) {
      return category;
    }
    if (category.children.length > 0) {
      const foundCategory = findCategoryByKey(key, category.children);
      if (foundCategory) {
        return foundCategory;
      }
    }
  }
  return null;
}

function findCategoryById(id, categories) {
  for (let category of categories) {
    if (category.id === id) {
      return category;
    }
    if (category.children.length > 0) {
      const foundCategory = findCategoryById(id, category.children);
      if (foundCategory) {
        return foundCategory;
      }
    }
  }
  return null;
}

const emit = defineEmits(["update:modelValue"]);

watch(selectedCategoryId, (newValue) => {
  if (newValue) {
    selectedCategory = findCategoryByKey(
      Object.keys(newValue)[0],
      categories.value,
    );
    emit("update:modelValue", selectedCategory);
  }
});
</script>

<template>
  <div class="space-y-2">
    <span>Category<span class="text-red-800">*</span></span>
    <div
      class="relative w-full cursor-default overflow-hidden rounded-lg bg-gray-100 text-left shadow-md focus:outline-none"
    >
      <TreeSelect
        v-model="selectedCategoryId"
        :options="categories"
        placeholder="Click on me"
        class="w-full bg-white outline-none text-gray-700 placeholder-gray-500 text-base p-4 border-0 dark:bg-gray-200"
      />
    </div>
    <div class="flex flex-col">
      <span class="text-xs">
        Did not find the category in the list?
        <router-link to="" class="text-blue-500 underline underline-offset-2"
          >Create new one.</router-link
        >
      </span>
      <span
        v-if="
          selectedCategory &&
          Object.keys(selectedCategory).length > 0 &&
          !selectedCategory.isLast
        "
        class="text-sm text-red-500"
      >
        Please, choose more specific category.
      </span>
    </div>
  </div>
</template>

<style>
.p-treeselect {
  @apply flex justify-between cursor-pointer;
}

.p-treeselect-label {
  @apply flex items-center gap-1 whitespace-nowrap p-0;
}

.p-treeselect-overlay {
  @apply mt-2 bg-white border-0 dark:bg-gray-200 dark:text-black;
}

.p-tree-node-content.p-tree-node-selectable:not(.p-tree-node-selected):hover {
  @apply bg-yellow-600 dark:bg-yellow-600 text-white;
}
</style>
