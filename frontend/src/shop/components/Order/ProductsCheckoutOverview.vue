<script setup>
import ProductCheckoutCard from "@/shop/views/Order/ProductCheckoutCard.vue";
import {useCartStore} from "@/stores/useCartStore.js";
import {useTruncator} from "@/composables/useTruncator.js";

const cart = useCartStore();

const truncator = useTruncator();
</script>

<template>
  <div class="flex flex-col gap-y-12 overflow-y-auto max-h-96">
    <product-checkout-card
        v-for="product in cart.cartItems"
        :price="product.unitPrice"
        :quantity="product.quantity"
        :product-name="truncator.truncateString(product.productTitle, 20).value"
        :image="product.productImage"
        :product-link="{
          id: product.productId,
          slug: product.productSlug
        }"
        :discount="product.discount"
    />
  </div>
  <div class="flex flex-col gap-y-6">
    <div class="flex justify-between">
      <span>Subtotal:</span>
      <span>${{ cart.totalPrice }}</span>
    </div>
    <hr class="h-[2px] bg-gray-300 border-0"/>
    <div class="flex justify-between">
      <span>Shipping:</span>
      <span>Free</span>
    </div>
    <hr class="h-[2px] bg-gray-300 border-0"/>
    <div class="flex justify-between">
      <span class="font-semibold">Total:</span>
      <span class="font-semibold">${{ cart.totalPrice }}</span>
    </div>
  </div>
</template>

<style scoped>

</style>