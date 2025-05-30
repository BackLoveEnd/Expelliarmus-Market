<template>
  <div>
    <button
        @click="handleButtonClick"
        :class="{
        'bg-white text-black underline underline-offset-4 decoration-2 decoration-dashed border border-green-600 hover:text-green-600':
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
import {useToastStore} from "@/stores/useToastStore.js";
import {useCartStore} from "@/stores/useCartStore.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import {ref, watch} from "vue";

const props = defineProps({
  productInfo: Object
});

const emit = defineEmits(["open-cart-modal"]);

const toast = useToastStore();

const cartStore = useCartStore();

const isInCart = ref(false);

async function handleButtonClick() {
  if (isInCart.value) {
    handleCartOpenModal();
  } else {
    await addToCart();
  }
}

async function addToCart() {
  await cartStore.addToCart(props.productInfo)
      .then(() => isInCart.value = true)
      .catch((e) => {
        toast.showToast(e?.response?.data?.message, defaultErrorSettings);
      });
}

function handleCartOpenModal() {
  emit("open-cart-modal");
}

watch(
    () => props.productInfo,
    (newValue) => {
      isInCart.value = cartStore.isProductInCart(props.productInfo.product_id, props.productInfo.variation_id);
    },
    {immediate: true, deep: true}
);
</script>

<style scoped></style>
