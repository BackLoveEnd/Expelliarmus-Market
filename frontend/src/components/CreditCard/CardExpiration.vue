<template>
  <div>
    <label for="card-expiration-input" class="mb-2 block text-sm font-medium"
      >Card expiration*</label
    >
    <div class="relative">
      <div
        class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3.5"
      >
        <svg
          class="h-4 w-4 text-gray-500 dark:text-gray-400"
          aria-hidden="true"
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          fill="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            fill-rule="evenodd"
            d="M5 5a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1h1a1 1 0 0 0 1-1 1 1 0 1 1 2 0 1 1 0 0 0 1 1 2 2 0 0 1 2 2v1a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V7a2 2 0 0 1 2-2ZM3 19v-7a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6.01-6a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm2 0a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Zm-10 4a1 1 0 1 1 2 0 1 1 0 0 1-2 0Zm6 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0Z"
            clip-rule="evenodd"
          />
        </svg>
      </div>
      <input
        id="card-expiration-input"
        ref="datepicker"
        v-model="value"
        type="text"
        class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 ps-9 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500"
        placeholder="MM/YY"
        required
      />
    </div>
  </div>
</template>

<script setup>
import Datepicker from "flowbite-datepicker/Datepicker";
import {
  computed,
  defineEmits,
  defineProps,
  onMounted,
  useTemplateRef,
} from "vue";

const props = defineProps({
  modelValue: String,
});

const emit = defineEmits(["update:modelValue"]);

const value = computed({
  get: () => props.modelValue,
  set: (newValue) => emit("update:modelValue", newValue),
});

onMounted(() => {
  const datepickerEl = document.getElementById("card-expiration-input");
  const datepicker = new Datepicker(datepickerEl, {
    format: "mm/yy",
    autohide: true,
  });

  datepickerEl.addEventListener("changeDate", (event) => {
    emit("update:modelValue", event.target.value);
    useTemplateRef("datepicker").hidden = true;
  });
});
</script>

<style scoped></style>
