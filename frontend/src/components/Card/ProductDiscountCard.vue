<template>
  <router-link
      class="w-272 h-auto flex flex-col gap-4 group hover:shadow-md rounded-md cursor-pointer transition-all duration-200"
      :to="`/shop/products/${props.discountedProduct?.product?.slug}`"
  >
    <div class="relative overflow-hidden">
      <img
          :src="props.discountedProduct?.product?.image"
          class="rounded product-image"
          alt="Product Image"
      />
      <div class="">
        <button
            @click="addToWishlist"
            :class="{ active: isInWishlist }"
            class="wishlist w-9 h-9 rounded-full flex items-center justify-center absolute top-3 right-3 bg-white"
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
                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
            />
          </svg>
        </button>
        <div
            class="absolute top-4 left-3 w-14 h-6 bg-[#db4444] text-center rounded-md"
        >
          <span class="text-xs text-white">{{ '-' + props.discountedProduct?.percentage }}</span>
        </div>
      </div>
      <button
          @click="addToCart"
          :class="{ 'bg-[#db4444]': isInCart, 'bg-black': !isInCart }"
          class="absolute bottom-0 left-0 w-full text-white text-center py-2 opacity-0 translate-y-full transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100"
      >
        {{ isInCart ? "Remove From Cart" : "Add To Cart" }}
      </button>
    </div>
    <div class="flex flex-col space-y-2 p-3">
      <p class="font-medium">{{ truncatedTitle }}</p>
      <div class="flex gap-3">
        <p class="font-semibold text-[#db4444]">{{ '$' + props.discountedProduct?.discountPrice }}</p>
        <p class="font-medium text-[#808080] line-through">{{ '$' + props.discountedProduct?.oldPrice }}</p>
      </div>
      <div class="flex items-center gap-x-2">
        <i class="pi pi-clock text-sm"></i>
        <span class="text-xs text-gray-600 font-semibold">Until {{
            new Date(props.discountedProduct?.endDate).toLocaleString()
          }}</span>
      </div>
      <star-rating :rating="4" :review-number="50"/>
    </div>
  </router-link>
</template>

<script setup>
import StarRating from "@/components/Card/StarRating.vue";
import {computed, inject, onUnmounted, ref} from "vue";
import {useAddToWishlist} from "@/composables/useAddToWishlist.js";

const emitter = inject("emitter");
const isInCart = ref(false);

const {isInWishlist, addToWishlist} = useAddToWishlist(emitter);

const props = defineProps({
  discountedProduct: Object
});

function addToCart() {
  isInCart.value = !isInCart.value;

  isInCart.value
      ? emitter.emit("add-to-cart")
      : emitter.emit("remove-from-cart");
}

const truncatedTitle = computed(() => {
  const title = props.discountedProduct?.product?.title || "";
  return title.length > 25 ? title.substring(0, 25) + "..." : title;
});

onUnmounted(() => {
  emitter.off("add-to-cart");
  emitter.off("remove-from-cart");
});
</script>

<style scoped>
.wishlist svg {
  stroke: currentColor;
}

.wishlist.active svg {
  fill: red;
  stroke: red;
}

.product-image {
  max-width: 500px;
  max-height: 500px;
  object-fit: cover;
}
</style>
