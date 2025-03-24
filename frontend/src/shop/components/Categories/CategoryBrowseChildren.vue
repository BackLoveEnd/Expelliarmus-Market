<script setup>
import {ref, watch} from "vue";
import {CategoriesShopService} from "@/services/CategoriesShopService.js";
import {useRoute, useRouter} from "vue-router";

const props = defineProps({
  categorySlug: String,
  initCategoryName: String
});

const categories = ref([]);

const router = useRouter();

const route = useRoute();

const parentCategory = ref({});
const mainCategorySlug = ref(null);

const emit = defineEmits(["category-selected"]);

const fetchChildrenCategories = async (categorySlug) => {
  try {
    const response = await CategoriesShopService.getChildrenForCategory(categorySlug);
    categories.value = response.data.data.map(cat => ({
      id: cat.id,
      slug: cat.attributes.slug,
      name: cat.attributes.name,
      icon: cat.attributes.icon,
      last: cat.attributes.last,
      parent: cat.attributes.parent
    }));

    if (!mainCategorySlug.value || !categories.value.length) {
      mainCategorySlug.value = categorySlug;
    }

    parentCategory.value = categories.value[0]?.parent || {};

    emit('category-selected', {name: parentCategory.value.name, slug: parentCategory.value.slug});
  } catch (error) {
    emit('category-selected', {name: props.initCategoryName, slug: props.categorySlug});
  }
};

watch(() => route.params.categorySlug, async (newSlug, oldSlug) => {
  if (!mainCategorySlug.value || oldSlug === mainCategorySlug.value) {
    mainCategorySlug.value = oldSlug || newSlug;
  }

  await fetchChildrenCategories(newSlug);
}, {immediate: true});

function showChild(category) {
  emit('category-selected', category);

  router.push({name: 'categories-browse', params: {categorySlug: category.slug}});
}
</script>

<template>
  <section class="flex flex-col gap-y-4">
    <div>
      <span class="text-3xl font-semibold">{{ parentCategory?.name }}</span>
    </div>

    <div v-if="categories.length" class="grid grid-cols-1 gap-4 w-full">
      <template v-for="category in categories" :key="category.id">
        <router-link
            v-if="!category.last"
            @click="showChild({ name: category.name, slug: category.slug })"
            :to="{ name: 'categories-browse', params: { categorySlug: category.slug } }"
            class="p-4 border rounded-lg cursor-pointer hover:bg-gray-100 flex items-center"
        >
          <img :src="category.icon" alt="icon" class="w-8 h-8 mr-2"/>
          <span>{{ category.name }}</span>
        </router-link>
        <div
            v-else
            @click="emit('category-selected', { name: category.name, slug: category.slug })"
            class="p-4 border rounded-lg cursor-pointer hover:bg-gray-100 flex items-center"
        >
          <img :src="category.icon" alt="icon" class="w-8 h-8 mr-2"/>
          <span>{{ category.name }}</span>
        </div>
      </template>

      <router-link
          v-if="mainCategorySlug && mainCategorySlug !== route.params.categorySlug"
          :to="{ name: 'categories-browse', params: { categorySlug: mainCategorySlug } }"
          class="p-2 hover:underline underline-offset-2"
      >
        <span>&lt; Previous</span>
      </router-link>
    </div>
  </section>
</template>
