<script setup>

import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import CancelledOrdersTable from "@/management/components/Orders/Cancelled/CancelledOrdersTable.vue";
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <div class="flex flex-col gap-y-4">
        <h1 class="font-semibold text-4xl">Cancelled Orders</h1>
        <span class="max-w-xl text-sm">Cancelled orders are orders that have been cancelled by users.</span>
      </div>
      <suspense>
        <cancelled-orders-table/>
        <template #fallback>
          <suspense-loader/>
        </template>
      </suspense>
    </section>
  </default-container>
</template>

<style scoped>

</style>