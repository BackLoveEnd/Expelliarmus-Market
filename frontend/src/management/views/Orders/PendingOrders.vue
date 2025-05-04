<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import PendingOrdersTable from "@/management/components/Orders/Pending/PendingOrdersTable.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <div class="flex flex-col gap-y-4">
        <h1 class="font-semibold text-4xl">Pending Orders</h1>
        <span class="max-w-xl text-sm">Pending orders are orders that have been placed but are either awaiting processing
          or have been processed and are waiting for delivery.</span>
        <div class="text-sm flex flex-col gap-y-2">
          <span>Order with status <strong>"pending"</strong> is order, that need to be processed by manager.</span>
          <span>Order with status <strong>"in progress"</strong> is order, that have been placed and waiting for delivery.</span>
        </div>
      </div>
      <suspense>
        <pending-orders-table/>
        <template #fallback>
          <suspense-loader/>
        </template>
      </suspense>
    </section>
  </default-container>
</template>

<style scoped>

</style>