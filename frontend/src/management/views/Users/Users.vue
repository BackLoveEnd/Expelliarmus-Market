<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import RegularCustomersTable from "@/management/components/Users/RegularCustomers/RegularCustomersTable.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
</script>

<template>
  <default-container>
    <div class="space-y-16 my-14">
      <section class="container mx-auto flex flex-col">
        <div>
          <h1 class="font-semibold text-4xl">Regular Customers</h1>
        </div>
      </section>
      <section class="container mx-auto">
        <suspense>
          <regular-customers-table/>
          <template #fallback>
            <suspense-loader/>
          </template>
        </suspense>
      </section>
    </div>
  </default-container>
</template>

<style scoped>

</style>