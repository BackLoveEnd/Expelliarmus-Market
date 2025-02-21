<template>
  <div class="absolute bottom-10 left-10 text-white space-y-4 max-w-60">
    <p class="text-2xl font-medium">{{ name }}</p>
    <p class="text-sm pb-4">{{ description }}</p>
    <router-link
      :to="link"
      class="underline underline-offset-8 decoration-[#878787] hover:font-medium hover:decoration-2 hover:decoration-white"
    >
      Shop now
    </router-link>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
  name: {
    type: String,
  },
  description: {
    type: String,
  },
  link: {
    type: String,
    default: "#",
  },
});
</script>

<style lang="scss" scoped></style>