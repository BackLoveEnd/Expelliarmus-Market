<template>
  <div class="space-y-2">
    <label v-if="label" :for="id"
      >{{ label }}<span v-if="required" class="text-red-800">*</span></label
    >
    <div
      class="flex items-center bg-gray-100 rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
    >
      <select
        v-model="countryCode"
        class="bg-gray-100 outline-none text-gray-700 pr-2"
        :required="required"
      >
        <option value="+380">+380 (UA)</option>
      </select>
      <input
        :id="id"
        :name="name"
        type="tel"
        maxlength="9"
        :placeholder="placeholder"
        v-model="phoneNumber"
        v-bind="$attrs"
        class="w-full bg-gray-100 outline-none text-gray-700 placeholder-gray-500 text-base pl-2"
        :required="required"
      />
    </div>
    <span v-if="error" class="text-sm text-red-600 ml-px">{{ error }}</span>
  </div>
</template>

<script setup>
import { computed, defineProps, ref, watch } from "vue";

const props = defineProps({
  id: String,
  name: String,
  placeholder: String,
  label: String,
  required: Boolean,
  modelValue: Object,
  error: String,
});

const emit = defineEmits(["update:modelValue"]);

const countryCode = ref(props.modelValue?.country_code || "+380");
const phoneNumber = ref(props.modelValue?.phone_number || "");

const value = computed({
  get: () => `${countryCode.value}${phoneNumber.value}`,
  set: (newValue) => {
    const parts = newValue.split("");
    countryCode.value = parts[0] || countryCode.value;
    phoneNumber.value = parts[1] || "";
  },
});

watch([countryCode, phoneNumber], ([newCountryCode, newPhoneNumber]) => {
  emit("update:modelValue", {
    country_code: newCountryCode,
    phone_number: newPhoneNumber,
  });
});
</script>
