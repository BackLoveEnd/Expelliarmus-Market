<script setup>
import {useAuthStore} from "@/stores/useAuthStore.js";

const auth = useAuthStore();
</script>

<template>
  <div class="w-full d-flex justify-between items-center">
    <span
    >Welcome back<span class="font-bold"
    >, {{ auth.firstName }}!</span
    ></span
    >
  </div>
</template>

<style scoped></style>
