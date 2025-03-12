<script setup>
defineProps({
  title: String,
  value: String | Number,
  icon: String
});
</script>

<template>
  <div class="p-4 rounded-md shadow-md flex flex-col items-center gap-y-2 flex-1">
    <i class="pi text-sm" :class="icon"></i>
    <span class="font-semibold">{{ value }}</span>
    <span class="text-sm">{{ title }}</span>
  </div>
</template>

<style scoped>

</style>