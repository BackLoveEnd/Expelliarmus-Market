<template>
  <div class="flex flex-col gap-y-6">
    <div
      v-for="(variation, index) in previewedVariations"
      :key="index"
      class=""
    >
      <div v-if="variation.type.name === 'color'">
        <colors :colors="colors" @color-changed="handleColorChanged" />
      </div>

      <div v-else-if="variation.attribute_view_type === 'radio'">
        <div class="flex items-center gap-x-4">
          <span class="text-xl">{{ variation.name }}:</span>
          <div v-for="(value, idx) in variation.value" :key="idx">
            <label class="inline-flex items-center">
              <input
                type="radio"
                :name="variation.name"
                :value="value"
                class="hidden peer"
                :checked="idx === 0"
                @change=""
              />
              <span
                class="w-auto h-auto p-1 flex justify-center items-center cursor-pointer border-2 border-gray-500 rounded-md peer-checked:bg-red-500 peer-checked:border-red-500 peer-checked:text-white text-black text-sm"
              >
                {{ value }}
              </span>
            </label>
          </div>
        </div>
      </div>

      <div v-else-if="variation.attribute_view_type === 'checkbox'">
        <div class="flex items-center gap-x-4">
          <span class="text-xl">{{ variation.name }}:</span>
          <div v-for="(value, idx) in variation.value" :key="idx">
            <label class="inline-flex items-center">
              <input type="checkbox" :value="value" class="hidden peer" />
              <span
                class="w-full h-8 flex justify-center items-center cursor-pointer border-2 border-gray-500 rounded-md peer-checked:bg-green-500 peer-checked:border-green-500 peer-checked:text-white text-black text-sm"
              >
                {{ value }}
              </span>
            </label>
          </div>
        </div>
      </div>

      <div v-else-if="variation.attribute_view_type.startsWith('dropdown')">
        <div class="flex flex-col gap-y-2">
          <span class="text-xl">{{ variation.name }}:</span>
          <select
            :multiple="variation.attribute_view_type === 'dropdown.checkbox'"
            class="border-2 rounded-md p-2"
          >
            <option
              v-for="(value, idx) in variation.value"
              :key="idx"
              :value="value"
            >
              {{ value }}
            </option>
          </select>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Colors from "@/components/Product/Main/Colors.vue";
import { computed, ref } from "vue";

const props = defineProps({
  previewedVariations: Array,
  variations: Array,
});

const selectedOptions = ref({});

const colors = computed(() => {
  const colorAttribute = props.previewedVariations.find(
    (attr) => attr.type?.name === "color",
  );

  return colorAttribute ? colorAttribute.value.map((color) => ({ color })) : [];
});

function handleColorChanged(color) {}
</script>

<style scoped></style>
