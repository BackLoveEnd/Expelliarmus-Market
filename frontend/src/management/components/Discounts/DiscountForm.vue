<script setup>
import {ErrorMessage, Field, Form} from "vee-validate";
import {DatePicker} from "primevue";
import {ref, watch} from "vue";
import * as yup from "yup";

const props = defineProps({
  originalPrice: Number,
  existsDiscount: Object | null
});

const discountError = ref(null);

const newPrice = ref(null);

const schema = yup.object().shape({
  percentage: yup
      .number()
      .transform((value, originalValue) => originalValue === "" ? null : value)
      .min(1, 'Percentage must be at least 1')
      .required('Percentage is required'),
  start_date: yup
      .date()
      .required('Start date is required'),
  end_date: yup
      .date()
      .min(yup.ref('start_date'), 'The end date must be after start date')
      .required('End date is required')
});

const emit = defineEmits(["form-submitted", "update-cancel", "form-updated"]);

const data = ref({
  percentage: null,
  start_date: null,
  end_date: null
});

function clearError() {
  discountError.value = null;
}

function onSubmit(values) {
  if (props.existsDiscount && new Date(values.start_date) < new Date()) {
    values.start_date = null;
  }

  if (props.existsDiscount) {
    emit("form-updated", values);
  } else {
    emit("form-submitted", values);
  }
}

const cancelUpdate = () => {
  emit("update-cancel");
};

watch((data), (newValue) => {
  if (newValue.percentage) {
    newPrice.value = parseFloat((props.originalPrice * (1 - (newValue.percentage / 100))).toFixed(2));
  } else {
    newPrice.value = null;
  }
  if (newValue.start_date && typeof newValue.start_date === 'string') {
    data.value.start_date = new Date(newValue.start_date);
  }
  if (newValue.end_date && typeof newValue.end_date === 'string') {
    data.value.end_date = new Date(newValue.end_date);
  }
}, {deep: true});

watch(() => props.existsDiscount, (newData) => {
  if (newData) {
    data.value.percentage = parseFloat(newData.percentage.replace('%', ''));
    data.value.start_date = new Date(newData.start_from);
    data.value.end_date = new Date(newData.end_at);
  }
}, {immediate: true});
</script>

<template>
  <Form @submit="onSubmit" :validation-schema="schema" class="flex flex-col gap-y-4">
    <div class="flex gap-x-4 items-center ml-10">
      <div class="flex flex-col justify-between">
        <label for="percentage">
          Percentage
          <span class="text-red-800">*</span>
        </label>
        <div class="relative overflow-hidden cursor-default rounded-lg bg-gray-100 text-left shadow-md">
          <Field
              type="number"
              name="percentage"
              id="percentage"
              min="1"
              max="100"
              v-model="data.percentage"
              @input="clearError"
              class="w-full bg-white outline-none text-gray-700 placeholder-gray-500 text-base p-4"
              placeholder=""
          />
        </div>
        <ErrorMessage name="percentage" class="text-sm text-red-600"/>
      </div>
      <div class="flex items-center flex-col text-sm flex-1" v-if="newPrice">
        <span>Discount price will be:</span>
        <span class="font-semibold">{{ newPrice }}</span>
      </div>
    </div>
    <div class="flex justify-around gap-x-4">
      <div class="flex flex-col gap-y-2">
        <label for="start_date">
          Discount Start Date
          <span class="text-red-700">*</span>
        </label>
        <Field name="start_date" v-slot="{ field }" v-model="data.start_date">
          <DatePicker
              v-bind="field"
              v-model="data.start_date"
              :min-date="new Date()"
              showTime
              hourFormat="24"
              :manualInput="false"
              fluid
          />
        </Field>
        <ErrorMessage name="start_date" class="text-sm text-red-600"/>
      </div>
      <div class="flex flex-col gap-y-2">
        <label for="end_date">
          Discount End Date
          <span class="text-red-700">*</span>
        </label>
        <Field name="end_date" v-slot="{ field }" v-model="data.end_date">
          <DatePicker
              v-bind="field"
              v-model="data.end_date"
              showTime
              hourFormat="24"
              :min-date="new Date()"
              :manualInput="false"
              fluid
          />
        </Field>
        <ErrorMessage name="end_date" class="text-sm text-red-600"/>
      </div>
    </div>
    <div class="flex justify-around">
      <button
          type="button"
          v-if="existsDiscount"
          @click="cancelUpdate"
          class="bg-gray-500 p-2 hover:bg-gray-800 w-1/3 text-white text-sm rounded-md">
        Cancel
      </button>
      <button
          type="submit"
          v-if="existsDiscount"
          class="bg-blue-500 p-2 hover:bg-blue-800 w-1/3 text-white text-sm rounded-md">
        Update
      </button>
      <button
          type="submit"
          v-if="!existsDiscount"
          class="bg-blue-500 p-2 hover:bg-blue-800 w-1/3 text-white text-sm rounded-md">
        Save
      </button>
    </div>
  </Form>
</template>

<style scoped>
</style>
