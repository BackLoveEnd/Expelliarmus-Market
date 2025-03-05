<script setup>
import {ErrorMessage, Field, Form} from "vee-validate";
import {DatePicker} from "primevue";
import {reactive, ref, watch} from "vue";
import * as yup from "yup";

const props = defineProps({
  originalPrice: Number
});

const discountError = ref(null);

const newPrice = ref(null);

const schema = yup.object().shape({
  percentage: yup.number().min(1, 'Percentage must be at least 1').required('Percentage is required'),
  start_date: yup
      .date()
      .min(new Date(), 'The start date must be today or later')
      .required('Start date is required'),
  end_date: yup
      .date()
      .min(yup.ref('start_date'), 'The end date must be after start date')
      .required('End date is required')
});

const data = reactive({
  percentage: null,
  start_date: null,
  end_date: null
});

function clearError() {
  discountError.value = null;
}

function onSubmit(values) {

}

watch((data), (newValue) => {
  if (newValue.percentage) {
    newPrice.value = parseFloat((props.originalPrice * (1 - (newValue.percentage / 100))).toFixed(2));
  }
}, {deep: true});
</script>

<template>
  <Form @submit="onSubmit" :validation-schema="schema" class="flex flex-col gap-y-4">
    <div class="flex gap-x-4 items-center">
      <div class="flex flex-col justify-between">
        <label for="percentage">
          Percentage
          <span class="text-red-800">*</span>
        </label>
        <div class="relative w-1/2 overflow-hidden cursor-default rounded-lg bg-gray-100 text-left shadow-md">
          <Field
              type="number"
              name="percentage"
              id="percentage"
              v-model="data.percentage"
              @input="clearError"
              class="w-full bg-white outline-none text-gray-700 placeholder-gray-500 text-base p-4"
              placeholder=""
          />
        </div>
        <ErrorMessage name="percentage" class="text-sm text-red-600"/>
      </div>
      <div class="flex items-center flex-col text-sm" v-if="newPrice">
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
        <Field name="start_date" v-slot="{ field }">
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
        <Field name="end_date" v-slot="{ field }">
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
    <div class="flex justify-center">
      <button type="submit" class="bg-blue-500 p-2 hover:bg-blue-800 w-1/3 text-white text-sm rounded-md">Save</button>
    </div>
  </Form>
</template>

<style scoped>
</style>
