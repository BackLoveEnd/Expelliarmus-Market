<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import ProductPreviewDetails from "@/management/components/Product/Other/ProductPreviewDetails.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const props = defineProps({
  id: Number | String,
  slug: String,
});
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <h1 class="text-3xl font-semibold">Product Preview</h1>
      <main class="space-y-32">
        <Suspense>
          <ProductPreviewDetails :id="id" :slug="slug"/>
          <template #fallback>
            <suspense-loader/>
          </template>
        </Suspense>
      </main>
    </section>
  </default-container>
</template>

<style scoped></style>
