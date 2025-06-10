<template>
  <main>
    <section class="container mx-auto mt-20">
      <bread-crumbs :links="links"></bread-crumbs>
    </section>
    <div
        v-if="cartStore.totalItems > 0"
        class="space-y-20 first-of-type:mt-20 last-of-type:mb-20"
    >
      <section class="container mx-auto">
        <suspense>
          <cart-overview
              @total-price="handleTotalPrice"
              @updated-product="handleUpdatedProduct"
          />
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
          <div class="flex gap-x-4">
            <button
                type="button"
                @click="clearCart"
                class="px-12 py-4 bg-white border-2 border-gray-400 text-center hover:bg-gray-400 hover:text-white rounded-md"
            >
              Clear cart
            </button>
            <button
                v-if="productsToUpdate.length > 0"
                type="button"
                @click="updateProducts"
                v-tooltip.top="'You can skip this if you proceed to checkout, otherwise, update the cart to save changes.'"
                class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
            >
              Update cart
            </button>
          </div>
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
              <router-link
                  to="/checkout"
                  @click.prevent="useScrolling().scrollToTop()"
                  class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
                  aria-label="Proceed to Checkout"
              >
                Proceed to Checkout
              </router-link>
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
import BreadCrumbs from '@/components/Default/BreadCrumbs.vue';
import {ref} from 'vue';
import CartOverview from '@/shop/components/Cart/CartOverview.vue';
import SuspenseLoader from '@/components/Default/SuspenseLoader.vue';
import {useCartStore} from '@/stores/useCartStore.js';
import {useToastStore} from '@/stores/useToastStore.js';
import defaultSuccessSettings from '@/components/Default/Toasts/Default/defaultSuccessSettings.js';
import defaultErrorSettings from '@/components/Default/Toasts/Default/defaultErrorSettings.js';
import {useScrolling} from "@/composables/useScrolling.js";

const links = ref([
  {url: '/', name: 'Home'},
  {url: '/cart', name: 'Cart'},
]);

const toast = useToastStore();

const cartStore = useCartStore();

const totalPrice = ref(0);

const productsToUpdate = ref([]);

const clearCart = async () => {
  await cartStore.clearCart().then(() => {
    toast.showToast('Cart was cleared.', defaultSuccessSettings);
  });
};

const handleTotalPrice = (value) => {
  totalPrice.value = value;
};

const handleUpdatedProduct = (product) => {
  const index = productsToUpdate.value.findIndex((item) => item.productId === product.productId);

  if (index === -1) {
    productsToUpdate.value.push(product);
  } else {
    productsToUpdate.value[index] = product;
  }
};

const updateProducts = async () => {
  await cartStore.updateQuantity(productsToUpdate.value)
      .then(() => {
        toast.showToast('Cart was updated.', defaultSuccessSettings);

        productsToUpdate.value = [];
      })
      .catch((e) => {
        toast.showToast(e?.response?.data?.message, defaultErrorSettings);
      });
};


</script>

<style scoped></style>
