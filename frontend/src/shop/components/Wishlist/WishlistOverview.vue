<script setup>
import WishlistProductCard from '@/components/Card/WishlistProductCard.vue'
import { useWishlistStore } from '@/stores/useWishlistStore.js'
import { ref } from 'vue'

const wishlistStore = useWishlistStore()

const isLoading = ref(false)

async function getWishlist () {
  await wishlistStore.fetchWishlist()
      .then(() => {

      })
      .catch((e) => {

      })
}

async function loadMore () {
  isLoading.value = true

  await wishlistStore.fetchMoreWishlist()
      .then(() => {

      })
      .catch((e) => {

      })
      .finally(() => isLoading.value = false)
}

await getWishlist()
</script>

<template>
  <section class="container mx-auto space-y-16">
    <div class="flex justify-between items-center">
      <span class="text-xl">Wishlist ({{ wishlistStore.totalItems }})</span>
    </div>
    <div class="grid grid-cols-5 gap-11" v-if="wishlistStore.totalItems > 0">
      <wishlist-product-card
          v-for="product in wishlistStore.wishlistItems"
          :key="product.id"
          :id="product.id"
          :title="product.title"
          :slug="product.slug"
          :image="product.image"
          :price="product.price"
      />
      <div class="flex items-center justify-center">
        <div v-if="wishlistStore.wishlistItems.length < wishlistStore.totalItems">
          <button type="button" class="flex flex-col items-center gap-y-2" @click="loadMore">
            <i class="pi pi-sync text-4xl text-[#374151]" :class="{ 'pi-spin': isLoading }"></i>
            <span class="font-semibold text-[#374151]">Load more</span>
          </button>
        </div>
        <!--        <div v-else class="flex flex-col items-center gap-y-2">
                            <i class="pi pi-check text-green-500 text-3xl"></i>
                  <span class="text-[#374151] text-sm font-semibold">That all.</span>
                </div>-->
      </div>
    </div>
    <div v-else class="my-20">
      <div class="flex flex-col justify-center items-center space-y-4">
        <h1 class="text-8xl font-bold text-gray-800">Oops :(</h1>
        <p class="text-4xl font-medium text-gray-800">Your Wishlist Is Empty</p>
        <router-link
            to="/"
            class="mt-4 text-base text-yellow-600 hover:underline underline-offset-4"
        >Search for products
        </router-link>
      </div>
    </div>
  </section>
</template>

<style scoped>

</style>