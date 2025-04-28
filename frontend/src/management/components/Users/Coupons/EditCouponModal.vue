<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {reactive, ref, watch} from "vue";
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
  couponData: Object,
});

const today = new Date();
const tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);

const newCoupon = reactive({
  email: null,
  discount: null,
  expires_at: null,
});

const validationError = ref([]);

const toast = useToastStore();

const schema = yup.object().shape({
  email: yup.string().email().label("User email"),
  discount: yup
      .number()
      .required("Discount is required")
      .min(1, "Discount must be greater than or equal to 1")
      .max(100, "Discount must be less than or equal to 100"),
  expires_at: yup
      .date()
      .required('Expires date is required'),
});

const emit = defineEmits(["modal-close", "coupon-updated"]);

const closeModal = () => {
  emit("modal-close");
};

const clearData = () => {
  newCoupon.email = null;
  newCoupon.discount = null;
  newCoupon.expires_at = null;
};

async function updateCoupon() {
  await ShopCouponService.editCoupon({id: props.couponData.coupon, ...newCoupon})
      .then((response) => {
        if (response?.status === 200) {
          emit("coupon-updated", response?.data?.data?.attributes);

          clearData();

          closeModal();

          toast.showToast(
              "Coupon was updated successfully.",
              defaultSuccessSettings,
          );
        }
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

watch(
    () => props.couponData,
    (data) => {
      if (data) {
        newCoupon.email = data.user_email || null;
        newCoupon.discount = data.discount || null;
        newCoupon.expires_at = data.expires_at ? new Date(data.expires_at) : null;
      }
    },
    {immediate: true}
);
</script>

<template>
  <DefaultModal
      :isActive="isModalOpen"
      @close-modal="closeModal"
      max-width="max-w-2xl"
  >
    <template #modalHeading>Edit Coupon</template>

    <template #modalBody>
      <Form
          :validation-schema="schema"
          method="post"
          :id="`form-${props.type}`"
          @submit="updateCoupon"
      >
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
                  id="discount"
                  class="w-full bg-gray-100 outline-none text-gray-700 placeholder-gray-500 text-base"
                  placeholder="10"
              />
            </div>
            <ErrorMessage name="discount" class="text-sm text-red-600"/>
          </div>
        </div>

        <div class="my-8" v-if="type === 'personal'">
          <div class="space-y-2 w-full">
            <label for="email" class="">User Email</label>
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

      <div v-if="validationError.length" class="flex flex-col">
        <span v-for="error in validationError" :key="error" class="text-sm text-red-500">
          {{ Object.values(error)[0] }}
        </span>
      </div>
    </template>

    <template #modalFooter>
      <div class="flex justify-between">
        <button
            type="submit"
            :form="`form-${props.type}`"
            class="px-4 py-2 bg-blue-500 text-white rounded-md"
        >
          Update
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
