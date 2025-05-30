<template>
  <div class="mb-6 grid grid-cols-2 gap-8">
    <card-holder
      class="col-span-2 sm:col-span-1"
      v-model="cardHolder"
    ></card-holder>
    <card-number
      class="col-span-2 sm:col-span-1"
      v-model="cardNumber"
    ></card-number>
    <card-expiration v-model="cardExpiration"></card-expiration>
    <card-secret v-model="cardSecret"></card-secret>
  </div>
</template>

<script setup>
import CardHolder from "@/components/CreditCard/CardHolder.vue";
import CardNumber from "@/components/CreditCard/CardNumber.vue";
import CardExpiration from "@/components/CreditCard/CardExpiration.vue";
import CardSecret from "@/components/CreditCard/CartSecret.vue";
import { ref } from "vue";

const cardHolder = ref("");
const cardExpiration = ref("");
const cardNumber = ref("");
const cardSecret = ref("");
</script>

<style scoped></style>
