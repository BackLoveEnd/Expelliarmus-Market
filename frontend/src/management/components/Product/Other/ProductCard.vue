<script setup>
import { ref } from "vue";
import { useScrolling } from "@/composables/useScrolling.js";
import { ProductService } from "@/services/ProductService.js";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const props = defineProps({
  productsCategories: {
    type: Array,
    required: true,
  },
});

const scrollable = useScrolling();
const isLoading = ref(false);

const loadMore = async (category, index) => {
  if (!category.next || isLoading.value) {
    return;
  }

  isLoading.value = true;

  if (category.next === 2) {
    const data = await fetchProducts(
      `/management/categories/${category.id}/products?page=2&include=products`,
    );
    category.products.push(...data.products);
    category.next = data.next || null;
  } else {
    const data = await fetchProducts(category.next);
    category.products.push(...data.products);
    category.next = data.next || null;
  }

  isLoading.value = false;
};

async function fetchProducts(url) {
  return ProductService.getProductsByCategory(url)
    .then((response) => ({
      next: response?.data?.data?.links?.self?.href
        ? `${response.data.data.links.self.href}&include=products`
        : null,
      products: response?.data?.included.map((product) => product.attributes),
    }))
    .catch((e) => {
      return null;
    });
}
</script>

<template>
  <div v-if="productsCategories.length > 0">
    <template v-for="(category, index) in productsCategories" class="space-y-4">
      <h2 class="text-xl text-gray-600">
        Category -
        <span class="font-semibold text-black">{{ category.name }}</span>
        (Total: {{ category.total }})
      </h2>

      <div v-if="category.products.length > 0">
        <div
          class="flex flex-wrap gap-4"
          :class="[
            category.products.length <= 5
              ? 'justify-start gap-x-20'
              : 'justify-between',
          ]"
        >
          <div
            v-for="(product, index) in category.products"
            :key="index"
            class="max-w-[240px] my-8"
          >
            <div class="bg-transparent rounded-lg overflow-hidden shadow-md">
              <div class="p-5 pb-0 overflow-hidden">
                <img
                  :src="product.preview_image"
                  alt="Load More"
                  class="w-full h-full object-cover rounded-lg"
                />
              </div>
              <div class="p-5 space-y-2">
                <div class="text-sm text-center text-gray-900">
                  {{ product.title }}
                </div>
                <div class="flex flex-col items-start mt-2">
                  <span class="text-xs text-gray-500"
                    >Created: {{ product.created_at }}</span
                  >
                  <span class="text-xs text-gray-500"
                    >Status: {{ product.status }}</span
                  >
                  <span class="text-xs text-gray-500"
                    >Article: {{ product.article }}</span
                  >
                </div>
                <div class="flex gap-x-2 justify-between">
                  <RouterLink
                    :to="{
                      name: 'product-preview',
                      params: { id: product.id, slug: product.slug },
                    }"
                    @click="scrollable.scrollToTop()"
                    class="bg-indigo-100 text-xs rounded-md p-1 hover:bg-indigo-300"
                  >
                    Show Preview
                  </RouterLink>
                  <RouterLink
                    :to="{
                      name: 'product-edit',
                      params: { id: product.id, slug: product.slug },
                    }"
                    @click="scrollable.scrollToTop()"
                    class="bg-blue-400 text-white text-xs rounded-md p-1 hover:bg-blue-500"
                  >
                    Edit
                  </RouterLink>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div
          class="flex justify-center mt-4"
          v-if="category.total > category.products.length"
        >
          <button
            v-if="!isLoading"
            @click="loadMore(category, index)"
            class="rounded-full px-4 py-2 duration-300 text-sm text-white shadow-sm bg-gray-400 bg-auto border-2 hover:bg-gray-500"
          >
            Load More
          </button>
          <suspense-loader v-else />
        </div>
        <div v-else class="flex justify-center mt-2">
          <span class="text-sm text-gray-400">That's all.</span>
        </div>
      </div>
      <div v-else>
        <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
          <div class="mx-auto max-w-screen-sm text-center">
            <p
              class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white"
            >
              Products not found
            </p>
            <p class="mb-4 text-lg font-light text-gray-500 dark:text-gray-400">
              There is no products for
              <span class="font-semibold">{{ category.name }}</span> category
            </p>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>
