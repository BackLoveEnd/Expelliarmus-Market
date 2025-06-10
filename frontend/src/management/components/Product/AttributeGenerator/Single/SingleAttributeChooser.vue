<script setup>
import { types } from "@/utils/attributeTypes.js";
import BaseSelectOption from "@/components/Default/Inputs/BaseSelectOption.vue";
import { defineEmits, reactive, ref, watch } from "vue";
import { AutoComplete } from "primevue";

const emit = defineEmits(["update-attributes"]);

const props = defineProps({
  numberOfAttributes: Number | String,
  attributesForCategory: Array,
});

const selectedAttribute = reactive({
  name: null,
  type: null,
});
const filteredAttributes = ref([]);
const showTypeSelect = ref(false);

const attributesData = reactive({
  attribute_id: null,
  attribute_name: "",
  attribute_type: null,
});

const search = (event) => {
  setTimeout(() => {
    if (!event.query.trim().length) {
      filteredAttributes.value = [...props.attributesForCategory];
    } else {
      filteredAttributes.value = props.attributesForCategory.filter(
        (attribute) =>
          attribute.name.toLowerCase().startsWith(event.query.toLowerCase()),
      );
    }
  }, 250);
};

watch(
  attributesData,
  (newData) => {
    emit("update-attributes", newData);
  },
  { deep: true },
);

watch(
  selectedAttribute,
  (newValue) => {
    const isManualEntry = !props.attributesForCategory.some(
      (attr) => attr.name.toLowerCase() === newValue?.name?.name?.toLowerCase(),
    );

    showTypeSelect.value = isManualEntry;

    if (isManualEntry) {
      const matchedAttribute = props.attributesForCategory.find(
        (attr) => attr.name?.toLowerCase() === newValue.name?.toLowerCase(),
      );
      if (matchedAttribute?.id) {
        showTypeSelect.value = false;
        attributesData.attribute_id = matchedAttribute.id;
        attributesData.attribute_name = matchedAttribute.name;
        attributesData.attribute_type = matchedAttribute.type;
      } else {
        attributesData.attribute_id = null;
        attributesData.attribute_name = newValue.name;
        attributesData.attribute_type = {
          id: newValue.type,
          name: types.find((t) => t.value === newValue.type)?.type ?? "text",
        };
      }
    } else {
      showTypeSelect.value = false;
      attributesData.attribute_id = newValue.name?.id || null;
      attributesData.attribute_name = newValue.name.name;
      attributesData.attribute_type = newValue.name.type;
    }
  },
  { deep: true },
);
</script>

<template>
  <div class="flex gap-x-8">
    <AutoComplete
      option-label="name"
      v-model="selectedAttribute.name"
      :suggestions="filteredAttributes"
      placeholder="Enter attribute name"
      @complete="search"
      empty-search-message="No attribute found."
      dropdown
    ></AutoComplete>
    <div v-if="showTypeSelect">
      <base-select-option
        id="type"
        name="type"
        v-model="selectedAttribute.type"
        :options="types"
        placeholder="Select type"
        body-class="bg-white"
        input-class="bg-white"
        required
      />
    </div>
  </div>
</template>

<style scoped></style>
