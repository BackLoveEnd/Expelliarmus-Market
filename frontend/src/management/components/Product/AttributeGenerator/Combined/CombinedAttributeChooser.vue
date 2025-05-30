<script setup>
import { AutoComplete } from "primevue";
import BaseSelectOption from "@/components/Default/Inputs/BaseSelectOption.vue";
import { types } from "@/utils/attributeTypes.js";
import { defineEmits, reactive, ref, watch, watchEffect } from "vue";
import { viewTypes } from "@/utils/attrubiteViewSelectionType.js";

const props = defineProps({
  attributesForCategory: Array,
  numberOfAttributes: Number,
});

const emit = defineEmits(["update-combo-attributes"]);

const attributeViewTypes = reactive(
  Array.from({ length: props.numberOfAttributes }, () => "0"),
);

const filteredAttributes = ref([]);
const selectedAttributes = reactive(
  Array.from({ length: props.numberOfAttributes }, () => ({
    id: null,
    name: null,
    type: null,
    view_type: null,
  })),
);

const showTypeSelect = reactive(
  Array.from({ length: props.numberOfAttributes }, () => false),
);

const search = (event) => {
  setTimeout(() => {
    const query = event.query.trim().toLowerCase();
    filteredAttributes.value = query
      ? props.attributesForCategory.filter((attribute) =>
          attribute.name.toLowerCase().startsWith(query),
        )
      : [...props.attributesForCategory];
  }, 300);
};

const onUpdateModelValue = (attrIndex, selected) => {
  const isManualEntry = !props.attributesForCategory.some(
    (attr) => attr.name.toLowerCase() === selected?.name?.toLowerCase(),
  );

  showTypeSelect[attrIndex] = isManualEntry;

  if (isManualEntry) {
    const matchedAttribute = props.attributesForCategory.find(
      (attr) => attr.name.toLowerCase() === selected?.toLowerCase(),
    );

    if (matchedAttribute?.id) {
      showTypeSelect[attrIndex] = false;
      selectedAttributes[attrIndex] = {
        id: matchedAttribute.id,
        name: matchedAttribute.name,
        type: matchedAttribute.type,
        view_type: attributeViewTypes[attrIndex],
      };
    }
  } else {
    selectedAttributes[attrIndex] = {
      id: selected.id,
      name: selected.name,
      type: selected.type,
      view_type: attributeViewTypes[attrIndex],
    };
  }
};

watch(
  selectedAttributes,
  (newData) => {
    newData.forEach((attr, index) => {
      if (typeof attr.type !== "object") {
        attr.type = {
          id: attr.type,
          name: types.find((t) => t.value === attr.type)?.type ?? "text",
        };
      }
    });
    emit("update-combo-attributes", newData);
  },
  { deep: true },
);

watchEffect(() => {
  selectedAttributes.forEach((attr, index) => {
    attr.view_type = attributeViewTypes[index];
  });
});
</script>

<template>
  <div class="divide-y divide-gray-300 space-y-4">
    <div
      v-for="n in props.numberOfAttributes"
      :key="n"
      class="flex items-start flex-col"
    >
      <div class="flex gap-x-8 items-center w-full mt-2">
        <div class="flex flex-col gap-y-2">
          <label :for="`autocomplete${n}`">Attribute {{ n }}</label>
          <AutoComplete
            :id="`autocomplete${n}`"
            option-label="name"
            v-model="selectedAttributes[n - 1].name"
            :suggestions="filteredAttributes"
            placeholder="Attribute name"
            empty-search-message="No attribute found."
            @complete="search"
            @update:model-value="(value) => onUpdateModelValue(n - 1, value)"
            dropdown
          />
        </div>
        <base-select-option
          id="viewTypes"
          label="View Type"
          v-model="attributeViewTypes[n - 1]"
          :options="viewTypes"
          body-class="shadow-md"
          input-class="w-2/3"
          name="viewTypes"
          placeholder="Select view type"
          required
        />
      </div>
      <div v-if="showTypeSelect[n - 1]">
        <BaseSelectOption
          id="type"
          name="type"
          v-model="selectedAttributes[n - 1].type"
          :options="types"
          label="Attribute 1 Type"
          body-class="shadow-md"
          input-class="bg-white"
          placeholder="Select type"
        />
      </div>
    </div>
  </div>
</template>

<style>
.p-autocomplete {
  @apply w-64;
}

.p-autocomplete .p-autocomplete-input {
  @apply w-full;
}

.p-autocomplete-overlay {
  @apply w-64;
}
</style>
