<script setup>
import { ref } from "vue";
import * as yup from "yup";
import { ErrorMessage, Field, Form } from "vee-validate";
import { forgotPassword } from "@/utils/auth.js";
import { HttpStatusCode } from "axios";
import { useToast } from "vue-toastification";
import resetEmailSentSettings from "@/components/Default/Toasts/ResetPassword/resetEmailSentSettings.js";
import defaultErrorSetting from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import { emailRule } from "@/utils/validationRules.js";

const toast = useToast();

const email = ref(null);

const schema = yup.object().shape({
  email: emailRule(yup),
});

function sendRequest() {
  forgotPassword(email.value)
    .then((response) => {
      if (response.status === HttpStatusCode.Ok) {
        toast.success(
          "We sent you instructions to reset password. Please, check your inbox.",
          resetEmailSentSettings,
        );
      }
    })
    .catch((e) => {
      if (e.response?.data?.message) {
        toast.error(e.response.data.message, defaultErrorSetting);
      }
    });
}
</script>

<template>
  <main class="w-full grid min-h-screen">
    <div class="grid place-items-center">
      <div class="w-full max-w-md mx-auto p-6">
        <div
          class="mt-7 bg-white rounded-xl shadow-lg border-2 border-yellow-400"
        >
          <div class="p-4 sm:p-7">
            <div class="text-center">
              <h1 class="block text-2xl font-bold text-gray-800">
                Forgot password?
              </h1>
              <p class="mt-2 text-sm text-gray-600">
                Remember your password?
                <router-link
                  class="text-blue-600 decoration-2 hover:underline font-medium"
                  :to="{ name: 'login' }"
                >
                  Login here
                </router-link>
              </p>
            </div>

            <div class="mt-5">
              <Form
                :validation-schema="schema"
                method="post"
                @submit="sendRequest"
              >
                <div class="grid gap-y-4">
                  <div>
                    <label
                      for="email"
                      class="block text-sm font-semibold ml-1 mb-2"
                      >Your Account Email address</label
                    >
                    <div class="relative">
                      <Field
                        type="email"
                        id="email"
                        name="email"
                        v-model="email"
                        class="py-3 px-4 block w-full border-2 border-gray-200 rounded-md text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                        required
                      />
                    </div>
                    <ErrorMessage
                      class="text-xs text-red-600 mt-2"
                      id="email-error"
                      name="email"
                    >
                    </ErrorMessage>
                  </div>
                  <button
                    type="submit"
                    class="py-3 px-4 inline-flex justify-center items-center gap-2 rounded-md border border-transparent font-semibold bg-blue-500 text-white hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all text-sm"
                  >
                    Reset password
                  </button>
                </div>
              </Form>
            </div>
          </div>
        </div>
        <p
          class="mt-3 flex justify-center items-center text-center divide-x divide-gray-300"
        >
          <router-link
            to="/"
            class="pr-3.5 inline-flex items-center gap-x-2 text-sm text-gray-600 decoration-2 hover:underline hover:text-blue-600"
          >
            Go To Home
          </router-link>
          <router-link
            to="/contact"
            class="pl-3 inline-flex items-center gap-x-2 text-sm text-gray-600 decoration-2 hover:underline hover:text-blue-600"
          >
            Contact us!
          </router-link>
        </p>
      </div>
    </div>
  </main>
</template>

<style scoped></style>
