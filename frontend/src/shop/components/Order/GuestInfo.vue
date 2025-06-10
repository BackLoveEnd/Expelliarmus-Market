<script setup>
import {useForm} from "vee-validate";
import * as yup from "yup";
import BaseTextInput from "@/components/Default/Inputs/BaseTextInput.vue";
import PhoneInput from "@/components/Default/Inputs/PhoneInput.vue";
import {emailRule, firstNameRule, lastNameRule} from "@/utils/validationRules.js";

const emit = defineEmits(["submit"]);

const {values, errors, handleSubmit, defineField} = useForm({
  validationSchema: yup.object({
    first_name: firstNameRule(yup),
    last_name: lastNameRule(yup),
    email: emailRule(yup),
    address: yup.string().required("Address is required"),
    country_code: yup.string().required("Country code is required"),
    phone_number: yup.number().typeError("Phone must be a number").required("Phone is required"),
  }),
  initialValues: {
    first_name: "",
    last_name: "",
    email: "",
    address: "",
    country_code: "+380",
    phone_number: "",
  },
});

const [first_name, firstNameAttrs] = defineField("first_name");
const [last_name, lastNameAttrs] = defineField("last_name");
const [email, emailAttrs] = defineField("email");
const [address, addressAttrs] = defineField("address");
const [country_code, countryCodeAttrs] = defineField("country_code");
const [phone_number, phoneAttrs] = defineField("phone_number");

const phone = {
  country_code: country_code,
  phone_number: phone_number,
};

const submit = handleSubmit(() => {
  emit("submit", {...values});
});

defineExpose({submit});
</script>

<template>
  <form class="space-y-6" method="post">
    <section class="grid grid-cols-2 gap-8">
      <base-text-input
          id="first-name"
          name="first_name"
          v-model="first_name"
          v-bind="firstNameAttrs"
          label="First Name"
          :error="errors.first_name"
      />
      <base-text-input
          id="last-name"
          name="last_name"
          v-model="last_name"
          v-bind="lastNameAttrs"
          label="Last Name"
          :error="errors.last_name"
      />
      <base-text-input
          id="email"
          name="email"
          v-model="email"
          v-bind="emailAttrs"
          label="Email"
          :error="errors.email"
      />
      <base-text-input
          id="address"
          name="address"
          v-model="address"
          v-bind="addressAttrs"
          label="Address"
          :error="errors.address"
      />
    </section>
    <section>
      <phone-input
          id="phone"
          name="phone"
          label="Phone Number"
          :model-value="phone"
          :error="errors.phone_number || errors.country_code"
      />
    </section>
  </form>
</template>

<style scoped></style>
