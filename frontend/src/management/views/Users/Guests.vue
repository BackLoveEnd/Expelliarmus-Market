<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import GuestsTable from "@/management/components/Users/Guests/GuestsTable.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
</script>

<template>
  <default-container>
    <div class="space-y-16 my-14">
      <section class="container mx-auto flex flex-col">
        <div>
          <h1 class="font-semibold text-4xl">Guests Customers</h1>
        </div>
      </section>
      <section class="container mx-auto">
        <suspense>
          <guests-table/>
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