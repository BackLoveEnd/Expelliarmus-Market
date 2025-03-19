<script setup>
import { Switch } from "@headlessui/vue";
import StepPanel from "primevue/steppanel";
import Button from "primevue/button";
import { computed } from "vue";

const props = defineProps({
  modelValue: null,
});

const emit = defineEmits(["update:modelValue"]);

const value = computed({
  get: () => props.modelValue,
  set: (newValue) => emit("update:modelValue", newValue),
});
</script>

<template>
  <StepPanel v-slot="{ activateCallback }" value="1">
    <div class="flex flex-col h-48">
      <div
        class="rounded bg-surface-50 dark:bg-surface-950 flex-auto flex justify-center items-center font-medium"
      >
        <div class="w-full space-y-6">
          <p>
            In your product, are there any attributes that are always used in
            combination? For example, in clothing, color and size are typically
            combined.
          </p>
          <div class="flex items-center justify-center gap-x-2">
            <span>No</span>
            <Switch
              v-model="value"
              :class="modelValue ? 'bg-yellow-500' : 'bg-gray-300'"
              class="relative inline-flex h-[24px] w-[52px] shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-white/75"
            >
              <span class="sr-only">Use setting</span>
              <span
                aria-hidden="true"
                :class="modelValue ? 'translate-x-7' : 'translate-x-0'"
                class="pointer-events-none inline-block h-[20px] w-[20px] transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out"
              />
            </Switch>
            <span>Yes</span>
          </div>
        </div>
      </div>
    </div>
    <div class="flex pt-6 justify-end">
      <Button
        label="Next"
        icon="pi pi-arrow-right"
        @click="activateCallback('2')"
      />
    </div>
  </StepPanel>
</template>

<style scoped></style>
