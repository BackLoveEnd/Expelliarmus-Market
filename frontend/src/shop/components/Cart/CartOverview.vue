<script setup>
import {useCartStore} from "@/stores/useCartStore.js";
import {computed, ref, watch} from "vue";

const cartStore = useCartStore();

const emit = defineEmits(["total-price", "cart-empty", "updated-product"]);

const isInitialLoad = ref(true);

const totalPrice = computed(() => cartStore.totalPrice.toFixed(2));

async function getCart() {
  await cartStore.fetchCart()
      .then(() => (isInitialLoad.value = true))
      .catch((e) => {
        if (e.status === 404) {
          emit("cart-empty");
        }
      });
}

watch(
    () => cartStore.cartItems.map((item) => item.quantity),
    (newQuantities, oldQuantities) => {
      if (isInitialLoad.value) {
        isInitialLoad.value = false;
        return;
      }

      newQuantities.forEach((newQty, index) => {
        const oldQty = oldQuantities[index];
        if (newQty !== oldQty) {
          emit("updated-product", cartStore.cartItems[index]);
        }
      });
    },
    {deep: true}
);

watch(totalPrice, (newTotal) => {
  emit("total-price", newTotal);
});

await getCart();
</script>

<template>
  <article class="flex flex-col gap-y-10">
    <table class="w-full border-separate border-spacing-y-10">
      <thead>
      <tr class="rounded-md shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] bg-white">
        <th class="py-6 px-12 font-normal text-start">Product</th>
        <th class="py-6 px-12 font-normal">Option</th>
        <th class="py-6 px-12 font-normal">Price</th>
        <th class="py-6 px-12 font-normal">Quantity</th>
        <th class="py-6 px-12 font-normal">Subtotal</th>
      </tr>
      </thead>
      <tbody class="text-center">
      <tr
          v-for="(product, index) in cartStore.cartItems"
          :key="product.productId"
          class="rounded-md shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] bg-white relative"
      >
        <td class="py-6 px-12 font-normal text-start max-w-xs">
          <div class="flex items-center gap-x-4">
            <img
                :src="product.productImage"
                :alt="product.productTitle"
                class="max-w-14 max-h-14"
            />
            <span class="text-base font-normal">{{ product.productTitle }}</span>
          </div>
        </td>
        <td class="py-6 px-12 font-normal">
          <div class="flex flex-col items-center" v-if="product.variation !== null">
            <div class="flex gap-x-2 text-sm" v-for="variation in product.variation.data">
              <span>{{ variation.attribute_name }} - </span>
              <span>{{ variation.value }}</span>
            </div>
          </div>
          <i class="pi pi-minus" v-else></i>
        </td>
        <td class="py-6 px-12 font-normal">
          ${{ product.unitPrice.toFixed(2) }}
        </td>
        <td class="py-6 px-12 font-normal">
          <div class="flex justify-center items-center">
            <input
                type="number"
                min="1"
                class="w-14 h-12 border-2 border-gray-400 text-center rounded-md"
                v-model.number="product.quantity"
            />
          </div>
        </td>
        <td class="py-6 px-12 font-normal">
          ${{ (product.unitPrice * product.quantity).toFixed(2) }}
        </td>
        <td>
          <button
              @click="cartStore.removeFromCart(product.id)"
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
</template>

<style scoped>
</style>