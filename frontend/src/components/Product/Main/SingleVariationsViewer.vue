<template>
  <div class="flex flex-col gap-y-6">
    <div v-if="previewedVariation.type.name === 'color'">
      <colors :colors="colors"/>
    </div>

    <div v-else-if="previewedVariation.attribute_view_type === 'radio'">
      <div class="flex items-center gap-x-4">
        <span class="text-xl">{{ previewedVariation.name }}:</span>
        <div v-for="(item, idx) in previewedVariation.data" :key="idx">
          <label class="inline-flex items-center">
            <input
                type="radio"
                :name="previewedVariation.name"
                :value="item.value"
                class="hidden peer"
                @change="selectVariation(item)"
                :checked="idx === 0"
            />
            <span
                class="min-w-8 min-h-8 p-1 flex justify-center items-center cursor-pointer border-2 border-gray-500 rounded-md peer-checked:bg-red-500 peer-checked:border-red-500 peer-checked:text-white text-black text-sm"
            >
              {{ item.value }}
            </span>
          </label>
        </div>
      </div>
    </div>

    <div v-else-if="previewedVariation.attribute_view_type === 'checkbox'">
      <div class="flex items-center gap-x-4">
        <span class="text-xl">{{ previewedVariation.name }}:</span>
        <div v-for="(item, idx) in previewedVariation.data" :key="idx">
          <label class="inline-flex items-center">
            <input type="checkbox" :value="item.value" class="hidden peer"/>
            <span
                class="w-auto h-8 flex justify-center items-center cursor-pointer border-2 border-gray-500 rounded-md peer-checked:bg-green-500 peer-checked:border-green-500 peer-checked:text-white text-black text-sm"
            >
              {{ item.value }}
            </span>
          </label>
        </div>
      </div>
    </div>

    <div
        v-else-if="previewedVariation.attribute_view_type.startsWith('dropdown')"
    >
      <div class="flex flex-col gap-y-2">
        <span class="text-xl">{{ previewedVariation.name }}:</span>
        <select
            :multiple="
            previewedVariation.attribute_view_type === 'dropdown.checkbox'
          "
            class="border-2 rounded-md p-2"
        >
          <option
              v-for="(item, idx) in previewedVariation.data"
              :key="idx"
              :value="item.value"
              @change="selectVariation(item)"
          >
            {{ item.value }}
          </option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup>
import Colors from "@/components/Product/Main/Colors.vue";
import {computed, watch} from "vue";

const props = defineProps({
  previewedVariation: Object,
  variations: Array
});

const colors = computed(() => {
  return props.previewedVariation.type.name === "color"
      ? props.previewedVariation.data.map((color) => ({color: color.value}))
      : [];
});
const emit = defineEmits(["selected-option"]);

function selectVariation(item) {
  const selectedVariation = props.variations.find(
      (variation) => variation.attributes.id === item.id
  );

  emit("selected-option", selectedVariation);
}

watch(
    () => props.previewedVariation,
    (newVal) => {
      if (newVal?.data?.length) {
        selectVariation(newVal.data[0]);
      }
    },
    {immediate: true}
);
</script>

<style scoped></style>
