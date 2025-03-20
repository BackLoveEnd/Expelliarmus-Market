<script setup>
import SectionTitle from "@/components/Default/SectionTitle.vue";
import ProductCardSet from "@/components/Card/ProductCardSet.vue";
</script>

<template>
  <section-title :title="'This month'"/>
  <product-card-set
      :title="'Best Selling Products'"
      :button-name="'View All'"
  >

  </product-card-set>
</template>

<style scoped>

</style>