<template>
  <main class="space-y-20 first-of-type:mt-20 last-of-type:mb-20">
    <!-- WISHLIST -->
    <section class="container mx-auto space-y-16">
      <div class="flex justify-between items-center">
        <span class="text-xl">Wishlist (4)</span>
        <button class="px-12 py-4 border border-black flex items-center rounded-md justify-center hover:bg-[#db4444] hover:text-white hover:border-0">
          Move All To Bag
        </button>
      </div>
      <div class="grid grid-cols-5 gap-11">
        <wishlist-product-card/>
        <wishlist-product-card/>
        <wishlist-product-card/>
        <wishlist-product-card/>
        <wishlist-product-card/>
      </div>
    </section>

    <!-- SUGGESTIONS -->
    <section class="container mx-auto space-y-16">
      <div class="flex justify-between items-center">
        <section-title :title="'Just For You'"/>
        <router-link to="#" class="px-12 py-4 border border-black flex items-center rounded-md justify-center hover:bg-[#db4444] hover:text-white hover:border-0">
          See All
        </router-link>
      </div>
      <div class="grid grid-cols-5 gap-11">
        <product-card/>
        <product-card/>
        <product-card/>
        <product-card/>
        <product-card/>
      </div>
    </section>
  </main>
</template>

<script>
import ProductCard from "@/components/Card/ProductCard.vue";
import WishlistProductCard from "@/components/Card/WishlistProductCard.vue";
import SectionTitle from "@/components/Default/SectionTitle.vue";

export default {
  components: {SectionTitle, WishlistProductCard, ProductCard}

}
</script>

<style scoped>

</style>