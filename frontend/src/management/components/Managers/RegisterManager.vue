<script setup>
import {reactive, ref} from "vue";
import * as yup from "yup";
import {emailRule, firstNameRule, lastNameRule} from "@/utils/validationRules.js";
import {ErrorMessage, Field, Form} from "vee-validate";
import {managerRegister} from "@/utils/auth.js";
import {HttpStatusCode} from "axios";
import {useToastStore} from "@/stores/useToastStore.js";
import registerToastSettings from "@/components/Default/Toasts/Auth/registerToastSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";

const formData = reactive({
  email: null,
  first_name: null,
  last_name: null
});

const schema = yup.object().shape({
  email: emailRule(yup),
  first_name: firstNameRule(yup),
  last_name: lastNameRule(yup),
});

function clearError() {
  registerErrors.value = [];
}

function resetForm() {
  Object.assign(formData, {
    email: '',
    first_name: '',
    last_name: ''
  });
}


const registerErrors = ref([]);

function register() {
  managerRegister(formData)
      .then((response) => {
        if (response.status === HttpStatusCode.Created) {
          useToastStore().showToast(response?.data?.message, registerToastSettings);

          resetForm();
        }
      })
      .catch((e) => {
        if (e.response.data?.errors) {
          const rawErrors = e.response.data.errors;

          registerErrors.value = [];

          for (const [fieldKey, messages] of Object.entries(rawErrors)) {
            const cleanedKey = fieldKey.replace("data.attributes.", "");
            registerErrors.value.push({field: cleanedKey, message: messages[0]});
          }
        }

        if (e?.status === 429) {
          toast.showToast('Too many requests. Please, wait.', defaultErrorSettings);
        }
      });
}
</script>

<template>
  <section>
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto my-20 lg:py-0">
      <div
          class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
          <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
            Create Manager account
          </h1>
          <Form class="space-y-4 md:space-y-6" method="post" :validation-schema="schema" @submit="register">
            <div>
              <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Manager
                email</label>
              <Field
                  name="email"
                  id="email"
                  @input="clearError"
                  v-model="formData.email"
                  type="email"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="manager@gmail.com"
                  required
              />
              <ErrorMessage
                  class="text-red-600 text-sm"
                  name="email"
              ></ErrorMessage>
            </div>
            <div>
              <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Manager first
                name</label>
              <Field
                  name="first_name"
                  id="first_name"
                  @input="clearError"
                  v-model="formData.first_name"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="John"
                  required
              />
              <ErrorMessage
                  class="text-red-600 text-sm"
                  name="first_name"
              ></ErrorMessage>
            </div>
            <div>
              <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Manager last
                name</label>
              <Field
                  name="last_name"
                  id="last_name"
                  @input="clearError"
                  v-model="formData.last_name"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                  placeholder="Doe"
                  required
              />
              <ErrorMessage
                  class="text-red-600 text-sm"
                  name="last_name"
              ></ErrorMessage>
            </div>
            <button type="submit"
                    class="w-full text-white bg-yellow-500 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
              Create an account
            </button>
          </Form>
        </div>
      </div>
      <template v-if="registerErrors.length">
        <div class="flex justify-center items-center flex-col gap-y-2 my-6">
          <span class="text-sm text-red-600 font-bold" v-for="error in registerErrors" :key="error.field">
              {{ error.message }}
            </span>
        </div>
      </template>
    </div>
  </section>
</template>

<style scoped>

</style>