<template>
  <div>
    <button
      @click="handleButtonClick"
      :class="{
        'bg-white text-black underline underline-offset-4 decoration-2 decoration-dashed border border-gray-700 border-green-600 hover:text-green-600':
          isInCart,
        'bg-[#db4444] border border-[#db4444] text-white hover:bg-red-900':
          !isInCart,
      }"
      class="px-8 py-3 text-center rounded-md"
    >
      {{ isInCart ? "Show Cart" : "Buy Now" }}
    </button>
  </div>
</template>

<script setup>
import { inject, ref } from "vue";

const emitter = inject("emitter");
const emit = defineEmits(["open-cart-modal"]);

const isInCart = ref(false);

function handleButtonClick() {
  if (isInCart.value) {
    handleCartOpenModal();
  } else {
    addToCart();
  }
}

function addToCart() {
  isInCart.value = true;
  emitter.emit("add-to-cart");
}

function handleCartOpenModal() {
  emit("open-cart-modal");
}
</script>

<style scoped></style>
