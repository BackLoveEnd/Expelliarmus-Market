<script setup>
import StepPanel from "primevue/steppanel";
import Button from "primevue/button";
import CombinedAttributeSelection from "@/management/components/Product/AttributeGenerator/Combined/CombinedAttributeSelection.vue";
import SingleAttributeSelection from "@/management/components/Product/AttributeGenerator/Single/SingleAttributeSelection.vue";

const props = defineProps({
  attributesForCategory: Array,
  withCombinedAttr: Boolean,
  numberOfCombinations: Number | String | null,
  numberOfAttributes: Number | String | null,
});

const emit = defineEmits(["last-step"]);

const lastStep = (activateCallback, step) => {
  activateCallback(step);
  emit("last-step");
};
</script>

<template>
  <StepPanel v-slot="{ activateCallback }" value="3">
    <div class="flex flex-col h-48 overflow-y-auto">
      <div
        class="rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium"
      >
        <single-attribute-selection
          v-if="withCombinedAttr === false"
          :attributesForCategory="attributesForCategory"
          :number-of-attributes="numberOfAttributes"
        />
        <combined-attribute-selection
          v-else
          :attributesForCategory="attributesForCategory"
          :number-of-attributes="numberOfAttributes"
          :number-of-combinations="numberOfCombinations"
        />
      </div>
    </div>
    <div class="flex pt-6 justify-between">
      <Button
        label="Back"
        severity="secondary"
        icon="pi pi-arrow-left"
        @click="activateCallback('2')"
      />
      <Button
        label="Next"
        icon="pi pi-arrow-right"
        @click="lastStep(activateCallback, '4')"
      />
    </div>
  </StepPanel>
</template>

<style scoped></style>
