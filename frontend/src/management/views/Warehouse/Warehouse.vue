<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import WarehouseProductSearcher from "@/management/components/Warehouse/WarehouseProductSearcher.vue";
import {ref} from "vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import WarehouseProductViewer from "@/management/components/Warehouse/WarehouseProductViewer.vue";
import WarehouseTable from "@/management/components/Warehouse/WarehouseTable.vue";

const productId = ref(null);

const handleProductSelection = (id) => {
  productId.value = id;
};
</script>

<template>
  <default-container>
    <div class="space-y-16 my-14">
      <section class="container mx-auto flex flex-col">
        <div>
          <h1 class="font-semibold text-4xl">Warehouse</h1>
        </div>
      </section>
      <section class="container mx-auto flex flex-col items-center">
        <warehouse-product-searcher
            @selected-product="handleProductSelection"
        />
      </section>
      <section class="container mx-auto" v-if="productId">
        <suspense>
          <warehouse-product-viewer :product="productId"/>
          <template #fallback>
            <suspense-loader/>
          </template>
        </suspense>
      </section>
      <section class="container mx-auto flex flex-col items-center">
        <suspense>
          <warehouse-table/>
          <template #fallback>
            <suspense-loader/>
          </template>
        </suspense>
      </section>
    </div>
  </default-container>
</template>

<style scoped></style>
