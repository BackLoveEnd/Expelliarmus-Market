<script setup>
const props = defineProps({
  filterSection: Object,
});

const emit = defineEmits(["brand-selected"]);

function toggleBrand(brand) {
  brand.checked = !brand.checked;

  emit("brand-selected", brand);
}
</script>

<template>
  <div v-for="(brandCategory, brandIdx) in filterSection.items" :key="brandIdx" class="space-y-2">
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

<style scoped>

</style>