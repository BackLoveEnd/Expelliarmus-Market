<script setup>

import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import DeliveredOrderTable from "@/management/components/Orders/Delivered/DeliveredOrderTable.vue";
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <div class="flex flex-col gap-y-4">
        <h1 class="font-semibold text-4xl">Delivered Orders</h1>
        <span
            class="max-w-xl text-sm">Delivered orders are orders that have been delivered successfully to users.</span>
      </div>
      <suspense>
        <delivered-order-table/>
        <template #fallback>
          <suspense-loader/>
        </template>
      </suspense>
    </section>
  </default-container>
</template>

<style scoped>

</style>