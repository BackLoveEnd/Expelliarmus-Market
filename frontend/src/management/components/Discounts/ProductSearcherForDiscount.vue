<script setup>
import {ref} from "vue";
import {WarehouseService} from "@/services/Product/WarehouseService.js";
import {AutoComplete} from "primevue";

const searchable = ref(null);
const suggestions = ref([]);

const search = async (event) => {
  setTimeout(async () => {
    if (searchable.value?.trim() === "") {
      suggestions.value = [];
      return;
    }

    await WarehouseService.searchProduct(searchable.value)
        .then((response) => {
          suggestions.value = response?.data?.data;
        })
        .catch((e) => {
          suggestions.value = [];
        });
  }, 300);
};

const emit = defineEmits(["selected-product"]);

const handleProductSelection = (value) => {
  if (typeof value === "object") {
    emit("selected-product", value.id);
  }
};
</script>

<template>
  <div class="flex flex-col gap-y-4 text-center items-center w-full">
    <label class="font-semibold"
    >Search for product you want to apply the discount to.</label
    >
    <AutoComplete
        class="!w-1/2"
        v-model="searchable"
        option-label="attributes.title"
        :suggestions="suggestions"
        empty-search-message="No products found."
        @complete="search"
        @update:model-value="handleProductSelection"
        placeholder="Title, brand, article..."
    >
      <template #option="slotProps">
        <div class="flex items-center justify-between">
          <span>{{ slotProps.option.attributes.title }}</span>
          <span class="text-sm text-gray-400">{{
              slotProps.option.attributes.article
            }}</span>
        </div>
      </template>
      <template #header>
        <div class="font-medium px-3 py-2">Available Products</div>
      </template>
    </AutoComplete>
    <span
        class="text-xs text-gray-500">Note: only "published" or "not published" can get a discount.</span>
  </div>
</template>
