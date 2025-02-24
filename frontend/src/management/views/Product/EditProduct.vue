<script setup>
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import WarehouseContent from "@/management/components/Warehouse/WarehouseContent.vue";

const props = defineProps({
  id: Number | String,
  slug: String,
});
</script>

<template>
  <Suspense>
    <warehouse-content :id="id" :slug="slug"/>
    <template #fallback>
      <suspense-loader/>
    </template>
  </Suspense>
</template>

<style scoped></style>
