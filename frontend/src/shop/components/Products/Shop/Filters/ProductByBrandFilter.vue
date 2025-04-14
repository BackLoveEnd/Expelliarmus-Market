<script setup>
import {ref, watch} from "vue";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import PriceFilter from "@/shop/components/Products/AllProducts/Filters/PriceFilter.vue";
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";
import CategoryFilter from "@/shop/components/Products/AllProducts/Filters/CategoryFilter.vue";
import {CategoriesShopService} from "@/services/Category/CategoriesShopService.js";
import {useScrolling} from "@/composables/useScrolling.js";

const props = defineProps({
  brandSlug: String
});

const selectedFilters = ref([]);

const filters = ref([]);

const subCategories = ref([]);

const emit = defineEmits(["selected-filters", "categories-not-found"]);

const scrolling = useScrolling();

async function getCategoriesForBrand(brandSlug) {
  try {
    const response = await CategoriesShopService.getCategoriesForBrand(brandSlug);
    filters.value.push({
      name: 'Category',
      items: response?.data?.data?.map(category => ({
        id: category.id,
        label: category.attributes.name,
        slug: category.attributes.slug,
        value: category.id,
        checked: false
      }))
    });
  } catch (error) {
    if (error?.status === 404) {
      emit("categories-not-found");
    }
  }
}

async function getPriceMinMax(brandSlug) {
  await ProductsShopService.getMinMaxPricesForBrand(brandSlug)
      .then((response) => {
        const minPrice = response?.data?.data?.attributes?.min_price;
        const maxPrice = response?.data?.data?.attributes?.max_price;

        if (minPrice === 0 && maxPrice === 0) {
          return;
        }

        filters.value.push({
          name: "Price",
          items: [
            {
              label: "Price",
              value: [minPrice, maxPrice],
            }
          ]
        });
      })
      .catch((e) => {
      });
}

async function togglePrice(event) {
  selectedFilters.value = selectedFilters.value.filter((filter) => filter.name !== 'price');

  toggleFilter('price', event);
}

async function toggleCategory(category) {
  if (category.checked) {
    toggleFilter("category", category.id);
  } else {
    removeFilterValue("category", category.id);

    subCategories.value = subCategories.value.filter(sub => sub.slug !== category.slug);
  }
}

function toggleFilter(filterName, value, extra) {
  const index = selectedFilters.value.findIndex((filter) => filter.name === filterName);

  if (index === -1) {
    selectedFilters.value.push({
      name: filterName,
      ...extra,
      value: [value]
    });
  } else {
    selectedFilters.value[index].value.push(value);
  }
}

function removeFilterValue(filterName, value) {
  const filterIndex = selectedFilters.value.findIndex((filter) => filter.name === filterName);

  if (filterIndex !== -1) {
    selectedFilters.value[filterIndex].value = selectedFilters.value[filterIndex].value.filter(val => val !== value);

    if (selectedFilters.value[filterIndex].value.length === 0) {
      selectedFilters.value.splice(filterIndex, 1);
    }
  }
}

const getSubCategories = (value) => {
  subCategories.value = value.value;
};

watch(() => selectedFilters, (newValue) => {
  emit("selected-filters", (newValue));
}, {
  deep: true
});

watch(
    () => props.brandSlug,
    (brandSlug) => {
      filters.value = [];

      getPriceMinMax(brandSlug);

      getCategoriesForBrand(brandSlug);
    },
    {immediate: true}
);

</script>

<template>
  <form class="hidden lg:block">
    <ul role="list" class="space-y-4 border-b border-gray-200 pb-6 text-sm font-medium text-gray-900">
      <li v-for="category in subCategories" :key="category.name" class="space-y-2">
        <span class="font-semibold">{{ category.name }}</span>
        <ul class="pl-4 space-y-2">
          <li v-for="sub in category.items" :key="sub.slug">
            <router-link
                @click.prevent="scrolling.scrollToTop()"
                :to="{ name: 'categories-browse', params: { categorySlug: sub.slug} }">
              {{ sub.name }}
            </router-link>
          </li>
        </ul>
      </li>
    </ul>

    <Disclosure as="div" v-for="(section, index) in filters" :key="index" class="border-b border-gray-200 py-6"
                v-slot="{ open }">
      <h3 class="-my-3 flow-root">
        <DisclosureButton
            class="flex w-full items-center justify-between bg-white py-3 text-sm text-gray-400 hover:text-gray-500">
          <span class="font-medium text-gray-900">{{ section.name }}</span>
          <span class="ml-6 flex items-center">
            <i v-if="!open" class="pi pi-plus" aria-hidden="true"/>
            <i v-else class="pi pi-minus" aria-hidden="true"/>
          </span>
        </DisclosureButton>
      </h3>

      <DisclosurePanel class="pt-6">
        <div
            class="space-y-4 max-h-60 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">
          <template v-if="section.name === 'Price'">
            <price-filter v-model="section.items[0].value" @update:modelValue="togglePrice"/>
          </template>

          <template v-if="section.name === 'Category'">
            <category-filter
                :filter-section="section"
                :section-index="index"
                @category-selected="toggleCategory"
                @sub-categories="getSubCategories"
            />
          </template>
        </div>
      </DisclosurePanel>
    </Disclosure>
  </form>
</template>
