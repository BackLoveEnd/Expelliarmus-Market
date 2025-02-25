<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <h1 class="text-3xl font-semibold">Trashed Products</h1>
      <main class="space-y-32">
        <Suspense>
          <template #fallback>
            <suspense-loader/>
          </template>
        </Suspense>
      </main>
    </section>
  </default-container>
</template>

<style scoped>

</style>