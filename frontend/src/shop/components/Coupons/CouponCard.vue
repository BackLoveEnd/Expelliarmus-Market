<script setup>
defineProps({
  title: String,
  couponCode: String,
  expiresAtDate: String,
  discount: Number,
  usageNumber: String | Number | null,
  type: String
});
</script>

<template>
  <div class="bg-white shadow-md rounded-xl p-4 w-full max-w-sm border-l-4"
       :class="[type === 'global' ? 'border-blue-400' : 'border-yellow-400']">
    <div class="flex justify-between items-center mb-3">
      <div class="flex items-center space-x-2">
        <i class="pi pi-tags text-xl" :class="[type === 'global' ? 'text-blue-500' : 'text-yellow-500']"></i>
        <h2 class="text-base font-semibold text-gray-800">{{ title }}</h2>
      </div>
      <div class="space-x-2">
        <span class="text-xs">Used: {{ usageNumber }}.</span>
        <span class="text-xs">Limit: 3.</span>
      </div>
    </div>

    <div class="bg-gray-100 p-1 rounded-md mb-3 text-center">
      <p class="text-xs text-gray-600">Use this code:</p>
      <span class="text-base font-mono font-bold text-gray-800 tracking-widest">{{ couponCode }}</span>
    </div>

    <div class="flex justify-between text-xs text-gray-600">
      <div class="flex items-center space-x-1">
        <i class="pi pi-calendar text-sm"></i>
        <span>Valid until: <strong>{{ expiresAtDate }}</strong></span>
      </div>
      <div class="flex items-center space-x-1">
        <i class="pi pi-percentage text-sm"></i>
        <span><strong>{{ discount }}% Off</strong></span>
      </div>
    </div>
  </div>
</template>

<style scoped>

</style>