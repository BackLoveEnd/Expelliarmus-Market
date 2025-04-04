<template>
  <router-link
      @click.prevent="useScrolling().scrollToTop()"
      class="w-272 h-auto flex flex-col gap-4 group hover:shadow-md rounded-md cursor-pointer p-3 transition-all duration-200"
      :to="`/shop/products/${props.discountedProduct?.id}/${props.discountedProduct?.slug}`"
  >
    <div class="relative overflow-hidden">
      <img
          :src="props.discountedProduct?.image"
          class="rounded product-image"
          :alt="props.discountedProduct?.title"
      />
      <div class="">
        <button
            @click.prevent.stop="wishlist.toggleWishlist()"
            class="wishlist w-9 h-9 rounded-full flex items-center justify-center absolute top-3 right-3 bg-white"
        >
          <i
              class="pi"
              :class="wishlist.isInWishlist() ? 'pi-heart-fill text-red-500' : 'pi-heart'">
          </i>
        </button>
        <div
            class="absolute top-4 left-3 w-14 h-6 bg-[#db4444] text-center rounded-md"
        >
          <span class="text-xs text-white">{{ '-' + props.discountedProduct?.discount?.percentage }}</span>
        </div>
      </div>
    </div>
    <div class="flex flex-col space-y-2 p-3">
      <p class="font-medium">{{ truncator.truncateString(props.discountedProduct?.title, 20) }}</p>
      <div class="flex gap-3">
        <p class="font-semibold text-[#db4444]">{{ '$' + props.discountedProduct?.discount?.discount_price }}</p>
        <p class="font-medium text-[#808080] line-through">{{
            '$' + props.discountedProduct?.discount?.original_price
          }}</p>
      </div>
      <div class="flex items-center gap-x-2">
        <i class="pi pi-clock text-sm"></i>
        <span class="text-xs text-gray-600 font-semibold">Until {{
            new Date(props.discountedProduct?.discount?.end_date).toLocaleString()
          }}</span>
      </div>
      <star-rating :rating="4" :review-number="50"/>
    </div>
  </router-link>
</template>

<script setup>
import StarRating from "@/components/Card/StarRating.vue";
import {useScrolling} from "@/composables/useScrolling.js";
import {useTruncator} from "@/composables/useTruncator.js";
import {useWishlistToggler} from "@/composables/useWishlistToggler.js";

const props = defineProps({
  discountedProduct: Object
});

const wishlist = useWishlistToggler({
  title: props.discountedProduct.title,
  id: props.discountedProduct.id,
  slug: props.discountedProduct.slug
});

const truncator = useTruncator();
</script>

<style scoped>
.product-image {
  max-width: 500px;
  max-height: 500px;
  object-fit: cover;
}
</style>
