<script setup>
import {ref} from "vue";
import Select from "primevue/select";
import DiscountInfoViewer from "@/management/components/Discounts/DiscountInfoViewer.vue";

const props = defineProps({
  variations: Array
});

const selectedVariation = ref(null);
</script>

<template>
  <section class="container mx-auto flex flex-col items-center gap-y-8">
    <Select
        :options="variations"
        style="width: 50%"
        placeholder="Choose option by SKU"
        :showClear="true"
        option-label="sku"
        v-model="selectedVariation"
    >
      <template #option="{ option }">
        <div class="flex gap-x-2 items-center">
          <span>{{ option.sku }}</span>
          <span class="text-red-500" v-if="option?.discount">(On Sale Now)</span>
        </div>
      </template>
    </Select>
    <div
        v-if="selectedVariation"
        class="w-full max-w-2xl p-6 bg-white rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700 space-y-4"
    >
      <h5
          class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
      >
        SKU - {{ selectedVariation.sku }}
      </h5>
      <div class="flex flex-col gap-y-4 text-sm">
        <div class="flex justify-between">
          <p class="font-semibold">Price: {{
              "$" + selectedVariation?.discount?.discount_price ?? selectedVariation.price
            }}</p>
          <p class="font-semibold">
            Total quantity: {{ selectedVariation.quantity }} unit(s)
          </p>
        </div>
        <p>Options combination:</p>
        <div class="flex gap-x-2">
          <div v-for="(attribute, index) in selectedVariation.attributes">
            <div class="flex gap-x-2" v-if="attribute.type.name === 'color'">
              <p>{{ attribute.name }}:</p>
              <input
                  type="color"
                  class="w-8 h-auto"
                  :value="attribute.value"
                  disabled
              />
              <span>|</span>
            </div>
            <div class="flex gap-x-2" v-else>
              <p>{{ attribute.name }}:</p>
              <p>{{ attribute.value }}</p>
            </div>
          </div>
        </div>
      </div>
      <div>
        <h5
            class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white text-center"
        >
          Discounts
        </h5>
        <div class="flex flex-col gap-2" v-if="selectedVariation.discount">
          <div class="flex justify-around items-center gap-14">
            <discount-info-viewer
                title="Percentage"
                :value="selectedVariation.discount.percentage"
                icon="pi-percentage"
            />
            <discount-info-viewer
                title="Old Price"
                :value="selectedVariation.discount.old_price"
                icon="pi-arrow-up text-red-500"
            />
            <discount-info-viewer
                title="Discount Price"
                :value="selectedVariation.discount.discount_price"
                icon="pi-arrow-down text-green-500"
            />
          </div>
          <div class="flex justify-around">
            <div class="p-4 rounded-md shadow-md flex flex-col items-center gap-y-2">
              <i class="pi pi-calendar-plus text-sm text-green-500"></i>
              <span class="text-sm font-semibold">{{ selectedVariation.discount.start_from }}</span>
              <span class="text-sm">Start Date</span>
            </div>
            <div class="p-4 rounded-md shadow-md flex flex-col items-center gap-y-2">
              <i class="pi pi-calendar-minus text-sm text-red-500"></i>
              <span class="text-sm font-semibold">{{ selectedVariation.discount.end_at }}</span>
              <span class="text-sm">End Date</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>

</style>