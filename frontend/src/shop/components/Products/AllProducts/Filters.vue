<script setup>
import {onMounted, ref, watch} from "vue";
import {CategoriesShopService} from "@/services/CategoriesShopService.js";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import {useScrolling} from "@/composables/useScrolling.js";
import {BrandsService} from "@/services/BrandsService.js";

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
    console.error("Ошибка загрузки категорий:", error);
  }
}

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
  } catch (error) {
    if (error?.status === 404) {

    }
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
      })
}

async function toggleCategory(category) {
  category.checked = !category.checked;

  if (category.checked) {
    toggleFilter("category", category.id);

    await getSubCategories(category.label, category.slug);

    await getBrands(category.id, category.label);
  } else {
    removeFilterValue("category", category.id);
    subCategories.value = subCategories.value.filter(sub => sub.slug !== category.slug);

    const brandSection = filters.value.find(section => section.name === "Brand");

    if (brandSection) {
      brandSection.items = brandSection.items.filter(item => item.categoryId !== category.id);
    }
  }
}

async function toggleBrand(brand) {
  console.log(brand)
  brand.checked = !brand.checked;

  if (brand.checked) {
    toggleFilter("brand", brand.id);
  } else {
    removeFilterValue("brand", brand.id);
  }

  console.log(selectedFilters.value)
}

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

onMounted(getRootCategories);
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
          <template v-if="section.name === 'Category'">
            <div v-for="(item, itemIdx) in section.items" :key="item.id" class="flex gap-3">
              <div class="flex h-5 shrink-0 items-center">
                <div class="group grid size-4 grid-cols-1">
                  <input :id="`filter-${index}-${itemIdx}`" :value="item.id"
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
              <label :for="`filter-${index}-${itemIdx}`" class="text-sm text-gray-600">{{ item.label }}</label>
            </div>
          </template>

          <template v-if="section.name === 'Brand'">
            <div v-for="(brandCategory, brandIdx) in section.items" :key="brandIdx" class="space-y-2">
              <span class="font-semibold block">{{ brandCategory.categoryName }}</span>
              <div v-for="(brand, brandItemIdx) in brandCategory.brands" :key="brand.id" class="flex gap-3 ml-4">
                <div class="flex h-5 shrink-0 items-center">
                  <div class="group grid size-4 grid-cols-1">
                    <input :id="`brand-${brandIdx}-${brandItemIdx}`" :value="brand.id"
                           type="checkbox" :checked="brand.checked"
                           @change="toggleBrand(brand)"
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
                <label :for="`brand-${brandIdx}-${brandItemIdx}`" class="text-sm text-gray-600">{{
                    brand.label
                  }}</label>
              </div>
            </div>
          </template>
        </div>
      </DisclosurePanel>
    </Disclosure>
  </form>
</template>


