<script setup>

import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import RegisterManager from "@/management/components/Managers/RegisterManager.vue";
</script>

<template>
  <default-container>
    <div class="space-y-16 my-14">
      <section class="container mx-auto flex flex-col">
        <div>
          <h1 class="font-semibold text-4xl">Managers</h1>
        </div>
      </section>
      <section class="container mx-auto">
        <register-manager/>
      </section>
    </div>
  </default-container>
</template>

<style scoped>

</style>