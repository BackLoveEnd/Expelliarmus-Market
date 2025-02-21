<script setup>
import * as yup from "yup";
import { useForm } from "vee-validate";
import { ref } from "vue";
import BaseTextInput from "@/components/Default/Inputs/BaseTextInput.vue";
import ConfirmPasswordChangeModal from "@/components/User/Profile/ConfirmPasswordChangeModal.vue";
import api from "@/utils/api.js";
import { useToastStore } from "@/stores/useToastStore.js";
import defaultErrorSetting from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import { HttpStatusCode } from "axios";
import { useRouter } from "vue-router";
import {
  currentPasswordRule,
  newPasswordRule,
  passwordConfirmationRule,
} from "@/utils/validationRules.js";
import { useAuthStore } from "@/stores/useAuthStore.js";

const isModalOpen = ref(false);
const router = useRouter();
const auth = useAuthStore();
const toastStore = useToastStore();

const { errors, defineField, handleSubmit, validate } = useForm({
  validationSchema: yup.object({
    current_password: currentPasswordRule(yup),
    password: newPasswordRule(yup),
    password_confirmation: passwordConfirmationRule(yup),
  }),
});

const [current_password, oldPassAttr] = defineField("current_password");
const [password, newPassAttr] = defineField("password");
const [password_confirmation, passwordConfirmAttr] = defineField(
  "password_confirmation",
);

const onSubmit = handleSubmit(async (values) => {
  await api()
    .put("/user/password", values)
    .then((response) => {
      if (response.status === HttpStatusCode.Ok) {
        auth.logout().then(() => {
          router.push({ name: "login" });
          toastStore.showToast(
            "Password was successfully changed. Please re-login.",
            defaultSuccessSettings,
          );
        });
      }
    })
    .catch((e) => {
      if (e.response?.data?.message) {
        toastStore.showToast(e.response.data.message, defaultErrorSetting);
      }
    })
    .finally(() => (isModalOpen.value = false));
});

const validateAndOpenModal = async () => {
  const validator = await validate();
  if (validator.valid) {
    isModalOpen.value = true;
  }
};
</script>

<template>
  <form class="space-y-6" method="post" id="passwordChange" @submit="onSubmit">
    <section class="flex w-full flex-col gap-y-4">
      <span class="text-xl font-medium text-[#db4444] mb-6"
        >Password Changes</span
      >
      <base-text-input
        id="old-password"
        name="current_password"
        type="password"
        v-model="current_password"
        v-bind="oldPassAttr"
        :error="errors.current_password"
        placeholder="Current Password"
      />
      <base-text-input
        id="new-password"
        name="password"
        placeholder="New Password"
        type="password"
        v-model="password"
        v-bind="newPassAttr"
        :error="errors.password"
      />
      <base-text-input
        id="password-confirmation"
        name="password_confirmation"
        placeholder="Confirm New Password"
        type="password"
        v-model="password_confirmation"
        v-bind="passwordConfirmAttr"
        :error="errors.password_confirmation"
      />
    </section>
    <section class="flex justify-end gap-x-8">
      <button
        type="button"
        @click="validateAndOpenModal"
        class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
      >
        Update Password
      </button>
    </section>
  </form>

  <confirm-password-change-modal
    :is-open="isModalOpen"
    form="passwordChange"
    :submit="onSubmit"
    @close="isModalOpen = false"
  />
</template>

<style scoped></style>
