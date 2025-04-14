<script setup>
import {onMounted, ref, watch} from "vue";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import {useScrolling} from "@/composables/useScrolling.js";
import {BrandsService} from "@/services/Brand/BrandsService.js";
import CategoryFilter from "@/shop/components/Products/AllProducts/Filters/CategoryFilter.vue";
import {CategoriesShopService} from "@/services/Category/CategoriesShopService.js";
import BrandFilter from "@/shop/components/Products/AllProducts/Filters/BrandFilter.vue";
import PriceFilter from "@/shop/components/Products/AllProducts/Filters/PriceFilter.vue";
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";

const selectedFilters = ref([]);

const subCategories = ref([]);

const filters = ref([]);

const scrolling = useScrolling();

const emit = defineEmits(["selected-filters"]);

async function getRootCategories() {
  try {
    const response = await CategoriesShopService.getRootCategories();
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

  }
}

async function getBrands(categoryId, categoryName) {
  await BrandsService.getProductBrandsByCategory(categoryId)
      .then((response) => {
        const brands = response?.data?.data?.map((brand) => ({
          id: brand.id,
          label: brand.attributes.brand_name,
          value: brand.id,
          checked: false,
          slug: brand.attributes.slug
        }));

        let brandSection = filters.value.find(section => section.name === "Brand");

        if (!brandSection) {
          brandSection = {
            name: "Brand",
            items: []
          };
          filters.value.push(brandSection);
        }

        const existingCategory = brandSection.items.find(item => item.categoryId === categoryId);

        if (existingCategory) {
          existingCategory.brands = [...existingCategory.brands, ...brands];
        } else {
          brandSection.items.push({
            categoryId,
            categoryName,
            brands
          });
        }
      })
      .catch((e) => {
      });
}

async function getPriceMinMax() {
  await ProductsShopService.getMinMaxPrices()
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

async function toggleCategory(category) {
  if (category.checked) {
    toggleFilter("category", category.id);

    await getBrands(category.id, category.label);
  } else {
    removeFilterValue("category", category.id);

    subCategories.value = subCategories.value.filter(sub => sub.slug !== category.slug);

    const brandSectionIndex = filters.value.findIndex(section => section.name === "Brand");

    if (brandSectionIndex !== -1) {
      const brandSection = filters.value[brandSectionIndex];

      brandSection.items = brandSection.items.filter(item => item.categoryId !== category.id);

      if (brandSection.items.length === 0) {
        filters.value.splice(brandSectionIndex, 1);
      }
    }
  }
}

async function toggleBrand(brand) {
  if (brand.checked) {
    toggleFilter("brand", brand.id);
  } else {
    removeFilterValue("brand", brand.id);
  }
}

async function togglePrice(event) {
  selectedFilters.value = selectedFilters.value.filter((filter) => filter.name !== 'price');

  toggleFilter('price', event);
}

const getSubCategories = (value) => {
  subCategories.value = value.value;
};

function toggleFilter(filterName, value) {
  const index = selectedFilters.value.findIndex((filter) => filter.name === filterName);

  if (index === -1) {
    selectedFilters.value.push({
      name: filterName,
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

watch(() => selectedFilters, (newValue) => {
  emit("selected-filters", (newValue));
}, {
  deep: true
});

onMounted(async () => {
  await getPriceMinMax();

  await getRootCategories();
});
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

          <template v-if="section.name === 'Brand'">
            <brand-filter :filter-section="section" @brand-selected="toggleBrand"/>
          </template>
        </div>
      </DisclosurePanel>
    </Disclosure>
  </form>
</template>


