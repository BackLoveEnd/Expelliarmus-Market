<template>
  <Combobox v-model="selected">
    <div class="relative mt-1">
      <span>Brand<span class="text-red-800">*</span></span>
      <div
        class="relative w-full cursor-default overflow-hidden rounded-lg bg-white text-left dark:bg-gray-200 shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm"
      >
        <ComboboxInput
          class="w-full bg-white outline-none text-gray-700 placeholder-gray-500 text-base p-4 dark:bg-gray-200"
          :displayValue="(brand) => brand?.brand_name"
          @change="searchBrand"
          placeholder="Start writing..."
          autocomplete="off"
          autocorrect="off"
          autocapitalize="off"
        />
        <ComboboxButton
          class="absolute inset-y-0 right-0 flex items-center pr-2"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-5 text-gray-400"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
            />
          </svg>
        </ComboboxButton>
      </div>
      <TransitionRoot
        leave="transition ease-in duration-100"
        leaveFrom="opacity-100"
        leaveTo="opacity-0"
        @after-leave="query = ''"
      >
        <ComboboxOptions
          class="absolute mt-1 z-50 max-h-60 w-full overflow-auto rounded-md bg-white dark:bg-gray-200 py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm"
        >
          <div
            v-if="filteredBrands.length === 0 && query !== ''"
            class="flex items-center justify-between relative cursor-default select-none px-4 py-2 text-gray-700"
          >
            <div>Nothing found. Continue writing...</div>
            <div v-if="isLoading" class="text-center">
              <div role="status">
                <svg
                  aria-hidden="true"
                  class="inline w-6 h-6 text-gray-200 animate-spin dark:text-gray-600 fill-yellow-600"
                  viewBox="0 0 100 101"
                  fill="none"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                    fill="currentColor"
                  />
                  <path
                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                    fill="currentFill"
                  />
                </svg>
                <span class="sr-only">Loading...</span>
              </div>
            </div>
          </div>

          <div
            v-else-if="filteredBrands.length === 0 && query === ''"
            class="relative cursor-default select-none px-4 py-2 text-gray-700"
          >
            Categories not found
          </div>

          <ComboboxOption
            v-for="brand in filteredBrands"
            :key="brand.id"
            :value="brand"
            v-slot="{ selected, active }"
          >
            <li
              class="relative cursor-default select-none py-2 pl-10 pr-4"
              :class="{
                'bg-yellow-600 text-white': active,
                'text-gray-900': !active,
              }"
            >
              <span
                class="block truncate"
                :class="{ 'font-medium': selected, 'font-normal': !selected }"
              >
                {{ brand.brand_name }}
              </span>
              <span
                v-if="selected"
                class="absolute inset-y-0 left-0 flex items-center pl-3"
                :class="{ 'text-white': active, 'text-yellow-600': !active }"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="size-6"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m4.5 12.75 6 6 9-13.5"
                  />
                </svg>
              </span>
            </li>
          </ComboboxOption>
        </ComboboxOptions>
      </TransitionRoot>
      <span class="text-xs">
        Did not find the brand in the list?
        <router-link to="" class="text-blue-500 underline underline-offset-2"
          >Create new one.</router-link
        >
      </span>
    </div>
  </Combobox>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from "vue";
import api from "@/utils/api.js";
import {
  Combobox,
  ComboboxButton,
  ComboboxInput,
  ComboboxOption,
  ComboboxOptions,
  TransitionRoot,
} from "@headlessui/vue";

let brands = reactive([]);
let selected = ref(null);
let query = ref("");
let isLoading = ref(false);

const props = defineProps({
  modelValue: null,
  brandId: Number,
});

let nextBrandsStack = null;
let totalBrands = 0;

let filteredBrands = computed(() => {
  return query.value === ""
    ? brands
    : brands.filter((brand) =>
        (brand.brand_name ?? "")
          .toLowerCase()
          .replace(/\s+/g, "")
          .includes((query.value ?? "").toLowerCase().replace(/\s+/g, "")),
      );
});

async function fetchBrands(link = "/management/brands") {
  if (brands.length !== 0 && totalBrands === brands.length) {
    return brands;
  }

  try {
    const response = await api().get(link);

    nextBrandsStack = response.data.links.next;
    totalBrands = response.data.meta.total;

    return response.data;
  } catch (e) {
    return [];
  }
}

onMounted(async () => {
  isLoading.value = true;

  await fetchBrands()
    .then((response) => {
      if (response.data?.length) {
        brands.push(...response.data.map((item) => item.attributes || {}));
      }

      if (props.brandId && brands.length) {
        selected.value =
          brands.find((brand) => brand.id === props.brandId) || null;
      }
    })
    .finally(() => {
      isLoading.value = false;
    });
});

function searchBrand(event) {
  query.value = event.target.value;

  if (
    filteredBrands.value.length === 0 &&
    query.value !== "" &&
    nextBrandsStack
  ) {
    isLoading.value = true;
    fetchBrands(nextBrandsStack)
      .then((responseData) => {
        brands.push(...responseData.data.map((item) => item.attributes || {}));
        isLoading.value = false;
      })
      .catch((error) => {
        isLoading.value = false;
      });
  }
}

const emit = defineEmits(["update:modelValue"]);

watch(selected, (value) => {
  emit("update:modelValue", value);
});
</script>
