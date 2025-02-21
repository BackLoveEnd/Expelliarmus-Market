<script setup>
import { computed } from "vue";

const props = defineProps({
  variations: Array,
});

const variations = computed(() =>
  props.variations.map((variation) => variation.attributes),
);
</script>

<template>
  <article class="grid grid-cols-2 gap-4 place-items-center">
    <div
      v-for="(variation, index) in variations"
      :key="variation.id"
      class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700"
    >
      <h5
        class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
      >
        SKU - {{ variation.sku }}
      </h5>
      <div class="flex flex-col gap-y-4 text-sm">
        <div class="flex justify-between">
          <p class="font-semibold">Price: {{ "$" + variation.price }}</p>
          <p class="font-semibold">
            Total quantity: {{ variation.quantity }} unit(s)
          </p>
        </div>
        <p>Options combination:</p>
        <div class="flex gap-x-2">
          <div v-for="(attribute, index) in variation.attributes">
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
    </div>
  </article>
</template>

<style scoped></style>
