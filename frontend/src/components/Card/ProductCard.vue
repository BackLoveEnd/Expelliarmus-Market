<template>
  <router-link
      @click.prevent="useScrolling().scrollToTop()"
      class="w-272 h-auto flex flex-col gap-4 group hover:shadow-md rounded-md p-3 cursor-pointer transition-all duration-200"
      :to="{ name: 'product-page', params: { productId: product.id, productSlug: product.slug }}"
  >
    <div class="relative overflow-hidden">
      <img
          :src="product.image"
          class="rounded"
          :alt="product.title"
      />
      <button
          @click.prevent.stop="wishlist.toggleWishlist()"
          class="wishlist w-9 h-9 rounded-full flex items-center justify-center absolute top-3 right-3 bg-white"
      >
        <i
            class="pi"
            :class="wishlist.isInWishlist() ? 'pi-heart-fill text-red-500' : 'pi-heart'">
        </i>
      </button>
    </div>
    <div class="flex flex-col space-y-2">
      <p class="font-medium">{{ truncator.truncateString(product.title, 25) }}</p>
      <p class="font-medium">{{ '$' + product.price }}</p>
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
  product: Object
});

const wishlist = useWishlistToggler({
  title: props.product.title,
  id: props.product.id,
  slug: props.product.slug
});

const truncator = useTruncator();
</script>

<style scoped>
.wishlist svg {
  stroke: currentColor;
}

.wishlist.active svg {
  fill: red;
  stroke: red;
}
</style>
