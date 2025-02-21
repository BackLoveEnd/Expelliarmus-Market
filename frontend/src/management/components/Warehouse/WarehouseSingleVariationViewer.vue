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
      class="w-full max-w-sm p-6 bg-white rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700"
    >
      <div class="flex flex-col gap-y-4 text-sm">
        <div class="flex gap-x-2">
          <div
            class="flex gap-x-2 mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
            v-if="variation.attribute_type === 'color'"
          >
            <p>{{ variation.attribute_name }}:</p>
            <input
              type="color"
              class="w-8 h-auto"
              :value="variation.value"
              disabled
            />
          </div>
          <div
            class="flex gap-x-2 mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
            v-else
          >
            <p>{{ variation.attribute_name }}:</p>
            <p>{{ variation.value }}</p>
          </div>
        </div>
        <div class="flex justify-between">
          <p class="font-semibold">Price: {{ "$" + variation.price }}</p>
          <p class="font-semibold">
            Total quantity: {{ variation.quantity }} unit(s)
          </p>
        </div>
      </div>
    </div>
  </article>
</template>

<style scoped></style>
