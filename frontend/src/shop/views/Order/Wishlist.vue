<template>
  <main class="space-y-20 first-of-type:mt-20 last-of-type:mb-20">
    <!-- WISHLIST -->
    <suspense>
      <wishlist-overview/>
      <template #fallback>
        <suspense-loader/>
      </template>
    </suspense>

    <!-- SUGGESTIONS -->
    <suspense>
      <suggestions-products/>
      <template #fallback>
        <suspense-loader/>
      </template>
    </suspense>
  </main>
</template>

<script>
import ProductCard from "@/components/Card/ProductCard.vue";
import WishlistProductCard from "@/components/Card/WishlistProductCard.vue";
import SectionTitle from "@/components/Default/SectionTitle.vue";
import WishlistOverview from "@/shop/components/Wishlist/WishlistOverview.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import SuggestionsProducts from "@/shop/components/Products/Sets/SuggestionsProducts.vue";

export default {
  components: {SuggestionsProducts, SuspenseLoader, WishlistOverview, SectionTitle, WishlistProductCard, ProductCard}
};
</script>

<style scoped>

</style>