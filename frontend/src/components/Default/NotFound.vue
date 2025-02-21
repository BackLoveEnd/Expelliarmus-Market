<script setup>

</script>

<template>
  <main class="grid place-items-center h-[80vh]">
    <div class="flex justify-center flex-col items-center space-y-10">
      <span class="text-8xl font-bold">404 Not Found</span>
      <span class="text-base font-semibold">Your visited page not found. You may go home page.</span>
      <router-link
          type="button"
          class="!mt-20 px-12 py-4 bg-[#DB4444] text-center cursor-pointer text-white hover:bg-red-900 rounded-md"
          to="/">Back to home page
      </router-link>
    </div>
  </main>
</template>

<style scoped>

</style>