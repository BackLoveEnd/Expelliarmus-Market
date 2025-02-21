<script setup>
import { reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
import * as yup from "yup";
import { ErrorMessage, Field, Form } from "vee-validate";
import { resetPassword } from "@/utils/auth.js";
import { useToastStore } from "@/stores/useToastStore.js";
import defaultErrorSetting from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import { HttpStatusCode } from "axios";
import {
  emailRule,
  passwordConfirmationRule,
  passwordRule,
} from "@/utils/validationRules.js";

const route = useRoute();
const router = useRouter();
const toastStore = useToastStore();

const resetForm = reactive({
  password: null,
  password_confirmation: null,
  email: route.query?.email || null,
});

const schema = yup.object().shape({
  email: emailRule(yup),
  password: passwordRule(yup),
  password_confirmation: passwordConfirmationRule(yup),
});

function resetPasswordRequest() {
  resetPassword(resetForm, route.params?.token)
    .then((response) => {
      if (response.status === HttpStatusCode.Ok) {
        toastStore.showToast(
          "Your password has been reset! You may log-in now.",
          defaultSuccessSettings,
        );

        router.push({ name: "login" });
      }
    })
    .catch((e) => {
      if (e.response?.data?.message) {
        toastStore.showToast(e.response.data.message, defaultErrorSetting);
      }
    });
}
</script>

<template>
  <section class="bg-gray-50">
    <div
      class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0"
    >
      <div
        class="w-full p-6 bg-white rounded-lg shadow md:mt-0 sm:max-w-md border border-yellow-400 sm:p-8"
      >
        <h2
          class="mb-1 text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white"
        >
          Change Password
        </h2>
        <Form
          class="mt-4 space-y-4 lg:mt-5 md:space-y-5"
          :validation-schema="schema"
          method="post"
          @submit="resetPasswordRequest"
        >
          <div>
            <label
              for="email"
              class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
              >Your email</label
            >
            <Field
              type="email"
              name="email"
              v-model="resetForm.email"
              id="email"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
              placeholder="john@doe.com"
              required=""
            />
            <ErrorMessage
              name="email"
              class="text-sm text-red-600"
            ></ErrorMessage>
          </div>
          <div>
            <label
              for="password"
              class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
              >New Password</label
            >
            <Field
              type="password"
              name="password"
              v-model="resetForm.password"
              id="password"
              placeholder="••••••••"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
              required=""
            />
            <ErrorMessage
              name="password"
              class="text-sm text-red-600"
            ></ErrorMessage>
          </div>
          <div>
            <label
              for="confirm-password"
              class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
              >Confirm password</label
            >
            <Field
              name="password_confirmation"
              type="password"
              id="confirm-password"
              placeholder="••••••••"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
              required=""
              v-model="resetForm.password_confirmation"
            />
            <ErrorMessage
              name="password_confirmation"
              class="text-sm text-red-600"
            ></ErrorMessage>
          </div>
          <button
            type="submit"
            class="w-full text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
          >
            Reset password
          </button>
        </Form>
      </div>
    </div>
  </section>
</template>

<style scoped></style>
