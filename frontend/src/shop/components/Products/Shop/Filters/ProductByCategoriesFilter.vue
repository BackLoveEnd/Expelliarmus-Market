<script setup>
import {ref, watch} from "vue";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import {BrandsService} from "@/services/Brand/BrandsService.js";
import PriceFilter from "@/shop/components/Products/AllProducts/Filters/PriceFilter.vue";
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";
import {CategoriesShopService} from "@/services/Category/CategoriesShopService.js";

const props = defineProps({
  categorySlug: String
});

const selectedFilters = ref([]);

const filters = ref([]);

const emit = defineEmits(["selected-filters"]);

async function getBrands(categorySlug) {
  await BrandsService.getProductBrandsByCategory(categorySlug)
      .then((response) => {
        filters.value.push({
          name: "Brand",
          items: response?.data?.data?.map((brand) => ({
            id: brand.id,
            label: brand.attributes.brand_name,
            value: brand.id,
            checked: false,
            slug: brand.attributes.slug
          }))
        });
      })
      .catch((e) => {
      });
}

async function getPriceMinMax(categorySlug) {
  await ProductsShopService.getMinMaxPricesForCategory(categorySlug)
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

async function getOptionAttributesForCategory(categorySlug) {
  await CategoriesShopService.getOptionAttributesForCategory(categorySlug)
      .then((response) => {
        response?.data?.data?.forEach((attr) => {
          const attrName = attr.attributes.name;
          const attrId = attr.attributes.id;
          const attrType = attr.attributes.type;

          const attrItems = attr.attributes.values.map((val) => ({
            label: val,
            value: val,
            checked: false,
            attrId: attrId,
            attrName: attrName,
            attrType: attrType
          }));

          filters.value.push({
            name: attrName,
            items: attrItems
          });
        });
      })
      .catch((e) => {
      });
}

async function toggleBrand(brand) {
  brand.checked = !brand.checked;

  if (brand.checked) {
    toggleFilter("brand", brand.id);
  } else {
    removeFilterValue("brand", brand.id);
  }
}

function toggleOption(attrName, item) {
  item.checked = !item.checked;

  if (item.checked) {
    toggleFilter(attrName, item.value, {id: item.attrId});
  } else {
    removeFilterValue(attrName, item.value);
  }
}

async function togglePrice(event) {
  selectedFilters.value = selectedFilters.value.filter((filter) => filter.name !== 'price');

  toggleFilter('price', event);
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

watch(() => selectedFilters, (newValue) => {
  emit("selected-filters", (newValue));
}, {
  deep: true
});

watch(
    () => props.categorySlug,
    async (newCategorySlug) => {
      filters.value = [];

      await getPriceMinMax(newCategorySlug);
      await getBrands(newCategorySlug);
      await getOptionAttributesForCategory(newCategorySlug);
    },
    {immediate: true}
);

</script>

<template>
  <form class="hidden lg:block">
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

          <template v-if="section.name === 'Brand'">
            <div v-for="(item, itemIdx) in section.items" :key="item.id" class="flex gap-3">
              <div class="flex h-5 shrink-0 items-center">
                <div class="group grid size-4 grid-cols-1">
                  <input :id="`filter-${index}-${itemIdx}`" :value="item.id"
                         type="checkbox" :checked="item.checked"
                         @change="toggleBrand(item)"
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
          <template v-if="section.name !== 'Price' && section.name !== 'Brand'">
            <div v-for="(item, itemIdx) in section.items" :key="itemIdx" class="flex gap-3 items-center">
              <div class="flex h-5 shrink-0 items-center">
                <div class="group grid size-4 grid-cols-1">
                  <input
                      :id="`filter-${index}-${itemIdx}`"
                      :value="item.value"
                      type="checkbox"
                      :checked="item.checked"
                      @change="toggleOption(section.name, item)"
                      class="col-start-1 row-start-1 appearance-none rounded-sm border border-gray-300 bg-white checked:border-indigo-600 checked:bg-indigo-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                  />
                  <svg
                      class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white"
                      viewBox="0 0 14 14" fill="none">
                    <path class="opacity-0 group-has-checked:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
              </div>
              <div class="w-6 h-4 rounded border" :style="{ backgroundColor: item.value }"
                   v-if="item.attrType === 'color'">
              </div>
              <label :for="`filter-${index}-${itemIdx}`" class="text-sm text-gray-600" v-else>
                {{ item.label }}
              </label>
            </div>
          </template>
        </div>
      </DisclosurePanel>
    </Disclosure>
  </form>
</template>
