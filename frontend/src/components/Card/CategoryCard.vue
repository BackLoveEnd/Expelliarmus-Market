<template>
  <router-link
      @click.prevent="useScrolling().scrollToTop()"
      :to="{ name: 'categories-browse', params: { categorySlug: categorySlug }, query: { name: categoryName }}"
      class="card w-48 h-40 border-2 border-gray-400 rounded-md flex flex-col justify-center items-center gap-y-4"
  >
    <div class="max-w-14 max-h-14">
      <img :src="categoryIcon" :alt="categoryName"/>
    </div>
    <div>
      <span class="text-base">{{ categoryName }}</span>
    </div>
  </router-link>
</template>

<script setup>
import {defineProps} from "vue";
import {useScrolling} from "@/composables/useScrolling.js";

const props = defineProps({
  categoryName: String,
  categoryIcon: String,
  categorySlug: String
});
</script>

<style scoped>
.card:hover {
  background-color: #db4444;
  border: 0;
}

.card:hover img {
  filter: invert(100%) brightness(100%);
}

.card:hover span {
  color: white;
}
</style>