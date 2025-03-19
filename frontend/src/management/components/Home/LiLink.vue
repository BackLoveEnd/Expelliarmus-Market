<script setup>
import {useScrolling} from "@/composables/useScrolling.js";

defineProps({
  title: String,
  icon: String,
  linkName: String
});

const scrollable = useScrolling();
</script>

<template>
  <router-link class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
               @click.prevent="scrollable.scrollToTop()"
               :to="{ name: linkName}">
    <div class="space-x-4">
      <i class="pi" :class="icon"></i>
      <span>{{ title }}</span>
    </div>
  </router-link>
</template>

<style scoped>

</style>