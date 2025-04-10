<script setup>
import {ErrorMessage, Field, Form} from "vee-validate";
import * as yup from "yup";
import {emailRule} from "@/utils/validationRules.js";
import {reactive, ref} from "vue";
import {useAuthStore} from "@/stores/useAuthStore.js";
import {useToastStore} from "@/stores/useToastStore.js";
import {useRouter} from "vue-router";
import ToastLogin from "@/components/Default/Toasts/Auth/ToastLogin.vue";
import loginToastSettings from "@/components/Default/Toasts/Auth/loginToastSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";

const loginErrors = ref([]);

const auth = useAuthStore();

const toast = useToastStore();

const router = useRouter();

const schema = yup.object().shape({
  email: emailRule(yup),
  password: yup.string().required().label("Password"),
});

const manager = reactive({
  email: null,
  password: null,
});

function clearError() {
  loginErrors.value = [];
}

function login() {
  auth
      .login(manager, true)
      .then((response) => {
        auth.fetchCurrentUser(true).then(async () => {
          toast.showToast(ToastLogin, loginToastSettings);

          await router.push({name: "manager-home"});
        });
      })
      .catch((e) => {
        if (e.response?.data?.errors) {
          const rawErrors = e.response.data.errors;

          loginErrors.value = [];

          for (const [fieldKey, messages] of Object.entries(rawErrors)) {
            const cleanedKey = fieldKey.replace("data.attributes.", "");
            loginErrors.value.push({field: cleanedKey, message: messages[0]});
          }
        }

        if (e?.status === 404) {
          toast.showToast('Invalid email or password.', defaultErrorSettings);
        }

        if (e?.status === 429) {
          toast.showToast('Too many requests. Please, wait.', defaultErrorSettings);
        }
      });
}
</script>

<template>
  <section class="bg-gray-50 dark:bg-gray-900">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <div
          class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
          <h1 class="text-xl text-center font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            Sign in to your account
          </h1>
          <Form class="space-y-4 md:space-y-6" @submit="login" method="post" :validation-schema="schema">
            <div>
              <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Your email</label>
              <Field
                  type="email"
                  name="email"
                  id="email"
                  v-model="manager.email"
                  @input="clearError"
                  class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  required
                  placeholder="your@email.com"
              />
              <ErrorMessage
                  name="email"
                  class="text-sm text-red-600"
              ></ErrorMessage>
            </div>
            <div>
              <label for="password"
                     class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
              <Field
                  type="password"
                  name="password"
                  v-model="manager.password"
                  @input="clearError"
                  id="password"
                  class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="********"
                  required
              />
              <ErrorMessage
                  name="password"
                  class="text-sm text-red-600"
              ></ErrorMessage>
            </div>
            <button type="submit"
                    class="w-full text-white bg-yellow-500 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
              Sign in
            </button>
          </Form>
        </div>
      </div>
      <template v-if="loginErrors.length">
        <div class="flex justify-center items-center flex-col gap-y-2 my-6">
          <span class="text-sm text-red-600 font-bold" v-for="error in loginErrors" :key="error.field">
              {{ error.message }}
            </span>
        </div>
      </template>
    </div>
  </section>
</template>

<style scoped>

</style>