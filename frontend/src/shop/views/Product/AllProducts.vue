<template>
  <div class="bg-white">
    <div>
      <main class="mx-auto container">
        <div class="flex items-baseline justify-between border-b border-gray-200 pt-24 pb-6">
          <h1 class="text-4xl font-bold tracking-tight text-gray-900">Products</h1>

          <div class="flex items-center">
            <Menu as="div" class="relative inline-block text-left">
              <div>
                <MenuButton
                    class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900">
                  Sort
                  <i class="-mr-1 ml-1 pi pi-chevron-down shrink-0 text-gray-400 group-hover:text-gray-500"
                     aria-hidden="true"/>
                </MenuButton>
              </div>

              <transition enter-active-class="transition ease-out duration-100"
                          enter-from-class="transform opacity-0 scale-95"
                          enter-to-class="transform opacity-100 scale-100"
                          leave-active-class="transition ease-in duration-75"
                          leave-from-class="transform opacity-100 scale-100"
                          leave-to-class="transform opacity-0 scale-95">
                <MenuItems
                    class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white ring-1 shadow-2xl ring-black/5 focus:outline-hidden">
                  <div class="py-1">
                    <MenuItem v-for="option in sortOptions" :key="option.name" v-slot="{ active }">
                      <button
                          type="button"
                          @click="handleSortChange(option)"
                          :class="[option.current ? 'font-medium text-gray-900' : 'text-gray-500', active ? 'bg-gray-100 outline-hidden' : '', 'block px-4 py-2 text-sm']">
                        {{
                          option.name
                        }}
                      </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>

            <button type="button" class="-m-2 ml-4 p-2 text-gray-400 hover:text-gray-500 sm:ml-6 lg:hidden"
                    @click="mobileFiltersOpen = true">
              <span class="sr-only">Filters</span>
              <i class="pi pi-filter" aria-hidden="true"/>
            </button>
          </div>
        </div>

        <section aria-labelledby="products-heading" class="pt-6 pb-24">
          <h2 id="products-heading" class="sr-only">Products</h2>

          <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4">
            <filters @selected-filters="handleSelectedFilters"/>

            <!-- Product grid -->
            <div class="lg:col-span-3">
              <suspense>
                <products-fetcher :filters="selectedFilters" :sort="selectedSort"/>
                <template #fallback>
                  <suspense-loader/>
                </template>
              </suspense>
            </div>
          </div>
        </section>
      </main>
    </div>
  </div>
</template>

<script setup>
import {ref} from 'vue';
import {Menu, MenuButton, MenuItem, MenuItems,} from '@headlessui/vue';
import Filters from "@/shop/components/Products/AllProducts/Filters/Filters.vue";
import ProductsFetcher from "@/shop/components/Products/AllProducts/ProductsFetcher.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const selectedFilters = ref([]);
const selectedSort = ref({});

const sortOptions = [
  {name: 'Price: Low to High', field: 'price', current: false, direction: 'asc'},
  {name: 'Price: High to Low', field: 'price', current: false, direction: 'desc'},
];

const handleSelectedFilters = (filters) => {
  selectedFilters.value = filters.value;
};

const handleSortChange = (option) => {
  sortOptions.forEach(opt => opt.current = false);
  option.current = true;
  selectedSort.value = {
    field: option.field,
    direction: option.direction
  };
};
</script>