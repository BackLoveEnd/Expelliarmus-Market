<template>
  <main class="space-y-20 first-of-type:mt-20 last-of-type:mb-20">
    <section class="container mx-auto">
      <bread-crumbs :links="links"></bread-crumbs>
    </section>
    <checkout-success v-if="isCheckoutSuccess" :orderId="orderId"/>
    <checkout-section @checkout-process-success="checkoutProcessSuccess" v-else/>
  </main>
</template>

<script setup>
import BreadCrumbs from "@/components/Default/BreadCrumbs.vue";
import {ref} from "vue";
import CheckoutSection from "@/shop/components/Order/CheckoutSection.vue";
import CheckoutSuccess from "@/shop/components/Order/CheckoutSuccess.vue";
import {useCartStore} from "@/stores/useCartStore.js";

const links = ref([
  {url: "/", name: "Home"},
  {url: "/cart", name: "Cart"},
  {url: "/checkout", name: "Checkout"},
]);

const isCheckoutSuccess = ref(false);

const cart = useCartStore();

const orderId = ref(null);

const checkoutProcessSuccess = (order_id) => {
  isCheckoutSuccess.value = true;

  orderId.value = order_id;

  cart.cartItems = [];
};
</script>

<style scoped></style>
