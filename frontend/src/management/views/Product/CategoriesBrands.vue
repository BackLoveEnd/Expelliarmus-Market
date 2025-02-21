<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import CategoriesTree from "@/management/components/Categories/CategoriesTree.vue";
import BrandsTable from "@/management/components/Brands/BrandsTable.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <div>
        <h1 class="font-semibold text-4xl">Categories</h1>
      </div>
      <div>
        <Suspense>
          <categories-tree />
          <template #fallback>
            <suspense-loader />
          </template>
        </Suspense>
      </div>
    </section>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <div>
        <h1 class="font-semibold text-4xl">Brands</h1>
      </div>
      <div>
        <Suspense>
          <brands-table />
          <template #fallback>
            <suspense-loader />
          </template>
        </Suspense>
      </div>
    </section>
  </default-container>
</template>

<style scoped></style>
