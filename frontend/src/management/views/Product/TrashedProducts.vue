<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import TrashedProductsList from "@/management/components/Product/Trashed/TrashedProductsList.vue";
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col min-h-full">
      <h1 class="text-3xl font-semibold">Trashed Products</h1>
      <main class="space-y-32 min-h-670 flex flex-grow items-center">
        <Suspense>
          <trashed-products-list/>
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