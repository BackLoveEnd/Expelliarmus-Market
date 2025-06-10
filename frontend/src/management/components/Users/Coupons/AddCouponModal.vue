<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {reactive, ref} from "vue";
import * as yup from "yup";
import {ErrorMessage, Field, Form} from "vee-validate";
import {useToastStore} from "@/stores/useToastStore.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings";
import defaultErrorSetting from "@/components/Default/Toasts/Default/defaultErrorSettings";
import {ShopCouponService} from "@/services/User/ShopCouponService.js";
import {DatePicker} from "primevue";

const props = defineProps({
  isModalOpen: Boolean,
  type: String,
});

const today = new Date();

const tomorrow = new Date(today);

tomorrow.setDate(tomorrow.getDate() + 1);

const newCoupon = reactive({
  coupon_code: null,
  email: null,
  discount: null,
  expires_at: null,
});

const validationError = ref([]);

const toast = useToastStore();

const schema = yup.object().shape({
  coupon_code: yup.string().nullable().label("Coupon Code"),
  email: yup.string().email().label("User email"),
  discount: yup
      .number()
      .required("Discount is required")
      .min(1, "Discount must be greater than or equal to 1")
      .max(100, "Discount must be less than or equal to 100"),
  expires_at: yup
      .date()
      .required('Expires date is required')
});

const emit = defineEmits(["modal-close", "coupon-added"]);

const closeModal = () => {
  emit("modal-close");
};

const clearData = () => {
  newCoupon.coupon_code = null;
  newCoupon.email = null;
  newCoupon.discount = null;
  newCoupon.expires_at = null;
};

async function createCoupon() {
  await ShopCouponService.createCoupon({...newCoupon, type: props.type})
      .then((response) => {
        emit("coupon-added", response?.data?.data?.attributes);

        clearData();

        closeModal();

        toast.showToast(
            "Coupon was created successfully.",
            defaultSuccessSettings,
        );
      })
      .catch((e) => {
        if (e.response?.status === 422) {
          validationError.value = useJsonApiFormatter().fromJsonApiErrorsFields(
              e.response?.data?.errors,
          );
        } else {
          closeModal();
          toast.showToast(
              "Unknown error, please try again or contact us.",
              defaultErrorSetting,
          );
        }
      });
}
</script>

<template>
  <DefaultModal
      :isActive="isModalOpen"
      @close-modal="closeModal"
      max-width="max-w-2xl"
  >
    <template #modalHeading>Create New Coupon</template>
    <template #modalBody>
      <Form
          :validation-schema="schema"
          method="post"
          :id="`form-${props.type}`"
          @submit="createCoupon"
      >
        <div class="my-8">
          <div class="space-y-2 w-full">
            <label for="coupon_code" class="">Coupon Code</label>
            <div
                class="flex items-center bg-gray-100 rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
            >
              <Field
                  type="text"
                  name="coupon_code"
                  v-model="newCoupon.coupon_code"
                  id="coupon_code"
                  class="w-full bg-gray-100 outline-none text-gray-700 placeholder-gray-500 text-base"
                  placeholder="NEW_YEAR"
              />
            </div>
            <div class="flex flex-col">
              <span class="text-sm">You may leave it empty. In that case, it will be created automatically.</span>
              <ErrorMessage name="coupon_code" class="text-sm text-red-600"/>
            </div>
          </div>
        </div>
        <div class="my-8 w-1/2">
          <div class="space-y-2 w-full">
            <label for="discount" class="">Discount Amount</label>
            <div
                class="flex items-center bg-gray-100 rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
            >
              <Field
                  type="number"
                  min="1"
                  max="100"
                  name="discount"
                  v-model="newCoupon.discount"
                  id="name"
                  class="w-full bg-gray-100 outline-none text-gray-700 placeholder-gray-500 text-base"
                  placeholder="10"
              />
            </div>
            <ErrorMessage name="discount" class="text-sm text-red-600"/>
          </div>
        </div>
        <div class="my-8" v-if="type === 'personal'">
          <div class="space-y-2 w-full">
            <label for="email" class="">User email</label>
            <div
                class="flex items-center bg-gray-100 rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
            >
              <Field
                  type="email"
                  name="email"
                  v-model="newCoupon.email"
                  id="email"
                  class="w-full bg-gray-100 outline-none text-gray-700 placeholder-gray-500 text-base"
                  placeholder="john@doe.com"
              />
            </div>
            <ErrorMessage name="email" class="text-sm text-red-600"/>
          </div>
        </div>
        <div class="my-4 w-1/2">
          <label for="expires_at">
            Coupon Expire Date
            <span class="text-red-700">*</span>
          </label>
          <Field name="expires_at" v-slot="{ field }" v-model="newCoupon.expires_at">
            <DatePicker
                v-bind="field"
                v-model="newCoupon.expires_at"
                showTime
                hourFormat="24"
                :min-date="tomorrow"
                :manualInput="false"
                fluid
            />
          </Field>
          <ErrorMessage name="expires_at" class="text-sm text-red-600"/>
        </div>
      </Form>
      <div v-if="validationError" class="flex flex-col">
        <span v-for="error in validationError" class="text-sm text-red-500">{{
            Object.values(error)[0]
          }}</span>
      </div>
    </template>
    <template #modalFooter>
      <div class="flex justify-between">
        <button
            type="submit"
            :form="`form-${props.type}`"
            class="px-4 py-2 bg-blue-500 text-white rounded-md"
        >
          Create
        </button>
        <button
            type="button"
            @click="closeModal"
            class="ml-2 px-4 py-2 bg-gray-300 rounded-md"
        >
          Cancel
        </button>
      </div>
    </template>
  </DefaultModal>
</template>

<style scoped></style>
