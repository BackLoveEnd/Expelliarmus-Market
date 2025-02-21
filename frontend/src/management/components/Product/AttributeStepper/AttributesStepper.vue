<script setup>
import Stepper from "primevue/stepper";
import StepList from "primevue/steplist";
import StepPanels from "primevue/steppanels";
import Step from "primevue/step";
import { inject, ref, watch } from "vue";
import FirstStepPanel from "@/management/components/Product/AttributeStepper/FirstStepPanel.vue";
import SecondStepPanel from "@/management/components/Product/AttributeStepper/SecondStepPanel.vue";
import FourthStepPanel from "@/management/components/Product/AttributeStepper/FourthStepPanel.vue";
import ThirdStepPanel from "@/management/components/Product/AttributeStepper/ThirdStepPanel.vue";

defineProps({
  attributesForCategory: null,
});

const withCombinedAttr = ref(false);
const numberOfAttributes = ref(1);
const numberOfCombinations = ref(null);

const chosenSingleAttr = inject("chosenSingleAttr");
const chosenCombinedAttr = inject("chosenCombinedAttr");

watch(withCombinedAttr, (value) => {
  numberOfAttributes.value = 1;
  chosenSingleAttr.value = {};
  chosenCombinedAttr.value = {};

  if (value) {
    numberOfCombinations.value = 1;
    numberOfAttributes.value = 2;
  } else {
    numberOfCombinations.value = null;
  }
});

const emit = defineEmits(["last-step"]);

const lastStep = () => {
  emit("last-step");
};

defineExpose({
  withCombinedAttr,
  numberOfAttributes,
  numberOfCombinations,
});
</script>

<template>
  <Stepper value="1" linear>
    <StepList>
      <Step value="1">Step I</Step>
      <Step value="2">Step II</Step>
      <Step value="3">Step III</Step>
      <Step value="4">Summarize</Step>
    </StepList>
    <StepPanels>
      <first-step-panel v-model="withCombinedAttr" />
      <second-step-panel
        :with-combined-attr="withCombinedAttr"
        v-model:number-of-attributes="numberOfAttributes"
        v-model:number-of-combinations="numberOfCombinations"
      />
      <third-step-panel
        :attributesForCategory="attributesForCategory"
        :with-combined-attr="withCombinedAttr"
        :number-of-attributes="numberOfAttributes"
        :number-of-combinations="numberOfCombinations"
        @last-step="lastStep"
      />
      <fourth-step-panel
        :with-combined-attr="withCombinedAttr"
        :number-of-attributes="numberOfAttributes"
        :number-of-combinations="numberOfCombinations"
        @last-step="lastStep"
      />
    </StepPanels>
  </Stepper>
</template>

<style scoped></style>
