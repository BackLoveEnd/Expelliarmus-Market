<template>
  <div class="w-272 h-352 flex flex-col gap-4 group">
    <div class="relative overflow-hidden">
      <img
        src="https://dummyimage.com/272x262/f5f5f5/000"
        class="rounded"
        alt="Product Image"
      />
      <button
        @click="removeFromWishlist"
        class="w-9 h-9 rounded-full flex items-center justify-center absolute top-3 right-3 bg-white"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
          />
        </svg>
      </button>
      <!--      <div class="absolute top-3 left-3 w-14 h-6 bg-[#db4444] text-center rounded-md">
              <span class="text-xs text-white">-40%</span>
            </div>-->
      <button
        @click="addToCart"
        :class="{ 'bg-[#db4444]': isInCart, 'bg-black': !isInCart }"
        class="absolute bottom-0 left-0 w-full text-white text-center py-2 opacity-0 translate-y-full transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100"
      >
        {{ isInCart ? "Remove From Cart" : "Add To Cart" }}
      </button>
    </div>
    <div class="flex flex-col space-y-2">
      <p class="font-medium">Product name</p>
      <p class="font-medium">$140</p>
      <!--      <div class="flex gap-3">
              <p class="font-medium text-[#db4444]">$140</p>
              <p class="font-medium text-[#808080] line-through">$140</p>
            </div>-->

      <!-- Check when product is on discount to apply another view -->

      <star-rating :rating="4" :review-number="50" />
    </div>
  </div>
</template>

<script setup>
import StarRating from "@/components/Card/StarRating.vue";
import { inject, ref } from "vue";

const emitter = inject("emitter");
const isInCart = ref(false);

function removeFromWishlist() {
  emitter.emit("remove-from-wishlist");
}

function addToCart() {
  isInCart.value = !isInCart.value;

  isInCart.value
    ? emitter.emit("add-to-cart")
    : emitter.emit("remove-from-cart");
}
</script>

<style scoped></style>
