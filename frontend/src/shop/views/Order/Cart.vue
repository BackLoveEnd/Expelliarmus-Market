<template>
  <main>
    <section class="container mx-auto mt-20">
      <bread-crumbs :links="links"></bread-crumbs>
    </section>
    <div
      v-if="products.length > 0"
      class="space-y-20 first-of-type:mt-20 last-of-type:mb-20"
    >
      <section class="container mx-auto">
        <article class="flex flex-col gap-y-10">
          <table class="w-full border-separate border-spacing-y-10">
            <thead>
              <tr
                class="rounded-md shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] bg-white"
              >
                <th class="py-6 px-12 font-normal text-start">Product</th>
                <th class="py-6 px-12 font-normal">Price</th>
                <th class="py-6 px-12 font-normal">Quantity</th>
                <th class="py-6 px-12 font-normal">Subtotal</th>
              </tr>
            </thead>
            <tbody class="text-center">
              <tr
                v-for="(product, index) in products"
                :key="product.id"
                class="rounded-md shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] bg-white relative"
              >
                <td class="py-6 px-12 font-normal text-start max-w-xs">
                  <div class="flex items-center gap-x-4">
                    <img
                      :src="product.image"
                      :alt="product.name"
                      class="max-w-14 max-h-14"
                    />
                    <span class="text-base font-normal">{{
                      product.name
                    }}</span>
                  </div>
                </td>
                <td class="py-6 px-12 font-normal">
                  ${{ product.productPrice }}
                </td>
                <td class="py-6 px-12 font-normal">
                  <div class="flex justify-center items-center">
                    <input
                      type="number"
                      min="1"
                      class="w-14 h-12 border-2 border-gray-400 text-center rounded-md"
                      v-model="product.quantity"
                    />
                  </div>
                </td>
                <td class="py-6 px-12 font-normal">${{ product.subTotal }}</td>
                <td>
                  <button
                    @click="removeProduct(product.id)"
                    class="absolute -top-3 -right-3 text-white"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="#db4444"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="size-7"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                      />
                    </svg>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </article>
        <div class="flex justify-between">
          <router-link
            to="/"
            class="px-12 py-4 bg-white border-2 border-gray-400 text-center hover:bg-[#db4444] hover:text-white rounded-md"
          >
            Continue Shopping
          </router-link>
          <router-link
            to="#"
            class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
          >
            Update card
            <!--?-->
          </router-link>
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
                <hr class="border-gray-300" />
                <div class="flex justify-between text-gray-700">
                  <span>Shipping:</span>
                  <span>Free</span>
                </div>
                <hr class="border-gray-300" />
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
import { computed, reactive, ref, watch } from "vue";

const links = ref([
  { url: "/", name: "Home" },
  { url: "/cart", name: "Cart" },
]);

const products = reactive([
  {
    id: 12321,
    name: "LCD Monitor",
    image: "https://dummyimage.com/54x54/000/fff",
    productPrice: 100,
    quantity: 1,
    subTotal: 100,
  },
  {
    id: 12331,
    name: "LCD Monitor",
    image: "https://dummyimage.com/54x54/000/fff",
    productPrice: 100,
    quantity: 1,
    subTotal: 100,
  },
]);

function removeProduct(id) {
  const index = products.findIndex((item) => item.id === id);
  if (index !== -1) {
    products.splice(index, 1);
  }
}

const totalPrice = computed(() => {
  return products.reduce((total, product) => total + product.subTotal, 0);
});

watch(
  () => products,
  (newProducts) => {
    newProducts.forEach(
      (product) => (product.subTotal = product.quantity * product.productPrice),
    );
  },
  { deep: true },
);
</script>

<style scoped></style>
