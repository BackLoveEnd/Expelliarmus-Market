<script setup>
import StepPanel from "primevue/steppanel";
import Button from "primevue/button";

defineProps({
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
  <StepPanel v-slot="{ activateCallback }" value="4">
    <div class="flex flex-col h-48">
      <div
        class="rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium"
      >
        <div class="w-full space-y-4">
          <span class="font-bold text-xl">Let's sum it up</span>

          <div v-if="withCombinedAttr" class="space-y-4">
            <span>You chose combined option with:</span>
            <ul class="font-light">
              <li>Number of combinations: {{ numberOfCombinations }}</li>
              <li>
                Number of attributes in combination:
                {{ numberOfAttributes }}
              </li>
            </ul>
            <span class="text-sm">If not, go back.</span>
          </div>
          <div v-else class="space-y-4">
            <span>You chose non-combined option with:</span>
            <p class="font-light">
              Number of attributes:
              {{ numberOfAttributes }}
            </p>
            <span class="text-sm">If not, go back.</span>
          </div>
        </div>
      </div>
    </div>
    <div class="pt-6">
      <Button
        label="Back"
        severity="secondary"
        icon="pi pi-arrow-left"
        @click="lastStep(activateCallback, '3')"
      />
    </div>
  </StepPanel>
</template>

<style scoped></style>
