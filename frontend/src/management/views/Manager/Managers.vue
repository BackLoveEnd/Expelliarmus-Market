<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import RegisterManager from "@/management/components/Managers/RegisterManager.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import ManagersTable from "@/management/components/Managers/ManagersTable.vue";
</script>

<template>
  <default-container>
    <div class="space-y-16 my-14">
      <section class="container mx-auto flex flex-col">
        <div>
          <h1 class="font-semibold text-4xl">Managers</h1>
        </div>
      </section>
      <section class="container mx-auto space-y-20">
        <register-manager/>
        <suspense>
          <managers-table/>
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