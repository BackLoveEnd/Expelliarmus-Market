<template>
  <router-link
      @click.prevent="useScrolling().scrollToTop()"
      class="w-272 h-auto flex flex-col gap-4 group hover:shadow-md rounded-md p-3 cursor-pointer transition-all duration-200"
      :to="{ name: 'product-page', params: { productId: props.id, productSlug: props.slug }}"
  >
    <div class="relative overflow-hidden">
      <img
          :src="image"
          class="rounded"
          :alt="title"
      />
      <button
          @click.prevent.stop="removeFromWishlist"
          class="w-9 h-9 rounded-full flex items-center justify-center absolute top-3 right-3 bg-white"
      >
        <i class="pi pi-trash"></i>
      </button>
    </div>
    <div class="flex flex-col space-y-2">
      <p class="font-medium">{{ truncator.truncateString(title, 25) }}</p>
      <p class="font-medium">{{ '$' + price }}</p>
      <star-rating :rating="4" :review-number="50"/>
    </div>
  </router-link>
</template>

<script setup>
import StarRating from "@/components/Card/StarRating.vue";
import {useTruncator} from "@/composables/useTruncator.js";
import {useWishlistStore} from "@/stores/useWishlistStore.js";
import {useToastStore} from "@/stores/useToastStore.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import {useScrolling} from "@/composables/useScrolling.js";

const props = defineProps({
  id: String | Number,
  title: String,
  slug: String,
  image: String,
  price: Number
});

const truncator = useTruncator();

const wishlist = useWishlistStore();

const toast = useToastStore();

const removeFromWishlist = async () => {
  await wishlist.removeFromWishlist(props.id)
      .then(() => {
        toast.showToast('Product was deleted.', defaultSuccessSettings);
      })
      .catch((e) => {
        toast.showToast(e?.response?.data?.message, defaultErrorSettings);
      });
};
</script>

<style scoped></style>
