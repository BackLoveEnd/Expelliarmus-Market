<template>
  <router-link
      @click.prevent="useScrolling().scrollToTop()"
      :to="{ name: 'brands-browse', params: { brandSlug: brand.slug } }"
      class="card w-48 h-40 border-2 border-gray-300 rounded-md flex flex-col justify-center items-center gap-y-4 transition-all duration-300"
  >
    <div class="flex items-center justify-center">
      <img
          :src="brand.logo"
          :alt="brand.name"
          class="brand-logo max-h-24 max-w-full object-contain"
      />
    </div>
  </router-link>
</template>

<script setup>
import {defineProps} from "vue";
import {useScrolling} from "@/composables/useScrolling.js";

const props = defineProps({
  brand: {
    type: Object,
    required: true,
  },
});
</script>

<style scoped>
.card:hover {
  @apply border-gray-500;
}
</style>
