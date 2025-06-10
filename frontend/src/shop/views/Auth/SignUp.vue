<script setup>
import * as yup from "yup";
import {ErrorMessage, Field, Form} from "vee-validate";
import {onMounted, reactive, ref} from "vue";
import {useScrolling} from "@/composables/useScrolling.js";
import {register} from "@/utils/auth.js";
import {HttpStatusCode} from "axios";
import {useRouter} from "vue-router";
import {useToastStore} from "@/stores/useToastStore.js";
import ToastRegistered from "@/components/Default/Toasts/Auth/ToastRegistered.vue";
import registerToastSettings from "@/components/Default/Toasts/Auth/registerToastSettings.js";
import {emailRule, firstNameRule, passwordConfirmationRule, passwordRule,} from "@/utils/validationRules.js";

const {scrollToTop} = useScrolling();

const router = useRouter();

const isLoading = ref(false);

const formData = reactive({
  email: null,
  first_name: null,
  password: null,
  password_confirmation: null,
});

const registerErrors = ref([]);

const schema = yup.object().shape({
  email: emailRule(yup),
  password: passwordRule(yup),
  first_name: firstNameRule(yup),
  password_confirmation: passwordConfirmationRule(yup),
});

function signUp() {
  isLoading.value = true;

  register(formData)
      .then((response) => {
        if (response.status === HttpStatusCode.Created) {
          useToastStore().showToast(ToastRegistered, registerToastSettings);
          router.push({name: "login"});
        }
      })
      .catch((e) => {
        if (e.response.data?.errors) {
          registerErrors.value = Object.values(e.response.data.errors).flat();
        }
      })
      .finally(() => isLoading.value = false);
}

onMounted(() => {
  const query = router.currentRoute.value.query;

  if (query.email && typeof query.email === "string") {
    formData.email = query.email;
  }

  if (query.name && typeof query.name === "string") {
    formData.first_name = query.name;
  }
});
</script>

<template>
  <main class="container mx-auto my-20">
    <div class="flex items-center justify-center">
      <img src="/default/auth-image.png" alt="Login image"/>
      <div class="mx-auto max-w-md">
        <div class="space-y-6 mb-12">
          <p class="text-4xl">Create an account</p>
          <p>Enter your details below</p>
        </div>
        <Form
            class="max-w-md mx-auto space-y-10"
            method="post"
            :validation-schema="schema"
            @submit="signUp"
        >
          <div class="relative z-0 w-full mb-5 group">
            <Field
                name="first_name"
                id="name"
                v-model="formData.first_name"
                class="block py-2.5 px-0 w-full text-sm text-black bg-transparent border-0 border-b-2 border-gray-400 appearance-none focus:outline-none focus:ring-0 focus:border-black peer"
                placeholder=" "
                required
            />
            <label
                for="name"
                class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-black peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"
            >First Name</label
            >
            <ErrorMessage
                class="text-red-600 text-sm"
                name="first_name"
            ></ErrorMessage>
          </div>
          <div class="relative z-0 w-full mb-5 group">
            <Field
                name="email"
                id="email"
                v-model="formData.email"
                type="email"
                class="block py-2.5 px-0 w-full text-sm text-black bg-transparent border-0 border-b-2 border-gray-400 appearance-none focus:outline-none focus:ring-0 focus:border-black peer"
                placeholder=" "
                required
            />
            <label
                for="email"
                class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-black peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"
            >Email address</label
            >
            <ErrorMessage
                class="text-red-600 text-sm"
                name="email"
            ></ErrorMessage>
          </div>
          <div class="relative z-0 w-full mb-5 group">
            <Field
                name="password"
                type="password"
                v-model="formData.password"
                id="password"
                class="block py-2.5 px-0 w-full text-sm text-black bg-transparent border-0 border-b-2 border-gray-400 appearance-none focus:outline-none focus:ring-0 focus:border-black peer"
                placeholder=" "
                required
            />
            <label
                for="password"
                class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-black peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"
            >Password</label
            >
            <ErrorMessage
                class="text-red-600 text-sm"
                name="password"
            ></ErrorMessage>
          </div>
          <div class="relative z-0 w-full mb-5 group">
            <Field
                type="password"
                name="password_confirmation"
                v-model="formData.password_confirmation"
                id="password_confirm"
                class="block py-2.5 px-0 w-full text-sm text-black bg-transparent border-0 border-b-2 border-gray-400 appearance-none focus:outline-none focus:ring-0 focus:border-black peer"
                placeholder=" "
                required
            />
            <label
                for="password_confirm"
                class="peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:left-0 peer-focus:text-black peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6"
            >Confirm password</label
            >
            <ErrorMessage
                class="text-red-600 text-sm"
                name="password_confirmation"
            ></ErrorMessage>
          </div>
          <div class="space-y-6">
            <button
                type="submit"
                class="w-full px-12 py-4 bg-[#DB4444] flex justify-center items-center cursor-pointer text-white hover:bg-red-900 rounded-md"
            >
              <i v-if="isLoading" class="pi pi-spin pi-spinner font-semibold"></i>
              <span v-else>Create an Account</span>
            </button>
            <p class="text-gray-600 text-center text-sm">
              Are you ready have an account?
              <span
              ><router-link
                  @click.prevent="scrollToTop"
                  class="text-black hover:underline underline-offset-8"
                  to="/log-in"
              >Log in</router-link
              ></span
              >
            </p>
            <div
                class="flex flex-col justify-center items-center gap-y-2"
                v-if="registerErrors.length > 0"
            >
              <span
                  class="text-sm text-red-600 font-bold"
                  v-for="error in registerErrors"
              >
                {{ error }}
              </span>
            </div>
          </div>
        </Form>
      </div>
    </div>
  </main>
</template>

<style scoped></style>
