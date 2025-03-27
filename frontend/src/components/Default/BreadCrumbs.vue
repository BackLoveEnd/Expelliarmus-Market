<template>
  <div class="flex space-x-2 text-sm">
    <template v-for="link in linksMapped" :key="link.url">
      <router-link :to="link.url"
                   :class="link.isLast ? 'text-black' : 'text-gray-400'"
      >
        {{ link.name }}
      </router-link>

      <span v-if="!link.isLast" class="text-gray-400">/</span>
    </template>
  </div>
</template>

<script setup>
import {computed, defineProps} from "vue";

const props = defineProps({
  links: {
    type: Array,
    required: true,
  },
});

const linksMapped = computed(() => {
  return props.links.map((link, index) => ({
    ...link,
    isLast: index === props.links.length - 1,
  }));
});
</script>

<style scoped></style>
