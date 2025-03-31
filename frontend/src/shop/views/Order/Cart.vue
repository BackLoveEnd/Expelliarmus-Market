<template>
  <main>
    <section class="container mx-auto mt-20">
      <bread-crumbs :links="links"></bread-crumbs>
    </section>
    <div
        v-if="! isCartEmpty"
        class="space-y-20 first-of-type:mt-20 last-of-type:mb-20"
    >
      <section class="container mx-auto">
        <suspense>
          <cart-overview @cart-empty="isCartEmpty = true"/>
          <template #fallback>
            <suspense-loader/>
          </template>
        </suspense>
        <div class="flex justify-between">
          <router-link
              to="/"
              class="px-12 py-4 bg-white border-2 border-gray-400 text-center hover:bg-[#db4444] hover:text-white rounded-md"
          >
            Continue Shopping
          </router-link>
          <button
              type="button"
              class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
          >
            Update card
          </button>
        </div>
      </section>
      <section class="container mx-auto">
        <div class="flex justify-end items-start gap-8">
          <div class="w-1/3 border-2 border-black rounded-lg p-6 space-y-6">
            <span class="font-medium text-xl text-start">Cart Total</span>
            <div class="flex flex-col items-center space-y-6">
              <div class="w-full space-y-4">
                <div class="flex justify-between text-gray-700">
                  <span>Subtotal:</span>
                  <span>${{ totalPrice }}</span>
                </div>
                <hr class="border-gray-300"/>
                <div class="flex justify-between text-gray-700">
                  <span>Shipping:</span>
                  <span>Free</span>
                </div>
                <hr class="border-gray-300"/>
                <div class="flex justify-between font-semibold text-lg">
                  <span>Total:</span>
                  <span>${{ totalPrice }}</span>
                </div>
              </div>
              <button
                  type="button"
                  class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
                  aria-label="Proceed to Checkout"
              >
                Proceed to Checkout
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>
    <div v-else class="my-20">
      <div class="flex flex-col justify-center items-center space-y-4">
        <h1 class="text-8xl font-bold text-gray-800">Oops :(</h1>
        <p class="text-4xl font-medium text-gray-800">Your Cart Is Empty</p>
        <router-link
            to="/"
            class="mt-4 text-base text-yellow-600 hover:underline underline-offset-4"
        >Search for products
        </router-link>
      </div>
    </div>
  </main>
</template>

<script setup>
import BreadCrumbs from "@/components/Default/BreadCrumbs.vue";
import {ref} from "vue";
import CartOverview from "@/shop/components/Cart/CartOverview.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const links = ref([
  {url: "/", name: "Home"},
  {url: "/cart", name: "Cart"},
]);

const isCartEmpty = ref();
</script>

<style scoped></style>
