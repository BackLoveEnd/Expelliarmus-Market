<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {reactive, ref} from "vue";
import * as yup from "yup";
import {ErrorMessage, Field, Form} from "vee-validate";
import {BrandsService} from "@/services/Brand/BrandsService.js";
import {useToastStore} from "@/stores/useToastStore.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings";
import defaultErrorSetting from "@/components/Default/Toasts/Default/defaultErrorSettings";

const props = defineProps({
  isModalOpen: Boolean,
});

const newBrand = reactive({
  name: null,
  description: null,
});

const validationError = ref([]);

const toast = useToastStore();

const schema = yup.object().shape({
  name: yup.string().required().label("Brand Name"),
  description: yup.string().nullable().label("Description"),
});

const emit = defineEmits(["modal-close", "brand-added"]);

const closeModal = () => {
  emit("modal-close");
};

const clearForm = () => {
  newBrand.name = null;
  newBrand.description = null;
};

async function createBrand() {
  await BrandsService.createBrand(newBrand)
      .then((response) => {
        if (response?.status === 201) {
          emit("brand-added", response?.data?.data?.attributes);
          clearForm();
          closeModal();
          toast.showToast(
              "Brand was created successfully.",
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
</script>

<template>
  <DefaultModal
      :isActive="isModalOpen"
      @close-modal="closeModal"
      max-width="max-w-2xl"
  >
    <template #modalHeading> Create New Brand</template>
    <template #modalBody>
      <Form
          :validation-schema="schema"
          method="post"
          id="form"
          @submit="createBrand"
      >
        <div class="my-8">
          <div class="space-y-2 w-full">
            <label for="name" class="">Brand Name</label>
            <div
                class="flex items-center bg-gray-100 rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
            >
              <Field
                  type="text"
                  name="name"
                  v-model="newBrand.name"
                  id="name"
                  class="w-full bg-gray-100 outline-none text-gray-700 placeholder-gray-500 text-base"
                  placeholder="Samsung"
              />
            </div>
            <ErrorMessage name="name" class="text-sm text-red-600"/>
          </div>
        </div>
        <div class="my-4">
          <div class="space-y-2 w-full">
            <label for="description" class=""
            >Brand Description (Optional)</label
            >
            <div
                class="flex items-center bg-gray-100 rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
            >
              <Field
                  as="textarea"
                  rows="3"
                  name="description"
                  v-model="newBrand.description"
                  id="description"
                  class="w-full bg-gray-100 outline-none text-gray-700 placeholder-gray-500 text-base"
                  placeholder="Samsung Electronics is a multinational electronics and information technology company headquartered in Suwon and the flagship company of the Samsung Group."
              />
            </div>
            <ErrorMessage name="description" class="text-sm text-red-600"/>
          </div>
        </div>
      </Form>
      <div v-if="validationError" class="flex flex-col">
        <span v-for="error in validationError" class="text-sm text-red-500">{{
          Object.values(error)[0]
        }}</span>
      </div>
    </template>
    <template #modalFooter>
      <button
          type="submit"
          form="form"
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
    </template>
  </DefaultModal>
</template>

<style scoped></style>
