<script setup>
import {useBreadCrumbStore} from "@/stores/useBreadCrumbStore.js";
import {onMounted, onUnmounted, ref} from "vue";
import {useRoute} from "vue-router";
import ProductsByBrand from "@/shop/components/Brands/ProductsByBrand.vue";
import BrandInfoOverview from "@/shop/components/Brands/BrandInfoOverview.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";

const breadCrumbStore = useBreadCrumbStore();

const route = useRoute();

const brandInfo = ref({});

const handleBrandInfo = (brand) => {
  brandInfo.value = brand;
};

onMounted(() => {
  const savedBreadcrumbs = localStorage.getItem('breadcrumb');

  const breadcrumbsParsed = JSON.parse(savedBreadcrumbs);

  if (breadcrumbsParsed?.breadcrumbs?.length) {
    breadCrumbStore.setBreadcrumbs(breadcrumbsParsed.breadcrumbs);
  } else {
    breadCrumbStore.setBreadcrumbs([
      {name: 'Brands', url: "/shop/brands"}
    ]);
  }
});

onUnmounted(() => {
  breadCrumbStore.clearBreadcrumbs();

  localStorage.removeItem('breadcrumb');
});
</script>

<template>
  <main class="container mx-auto my-10 space-y-10">
    <suspense>
      <brand-info-overview :brand-slug="route.params.brandSlug" @brand-info="handleBrandInfo"/>
      <template #fallback>
        <suspense-loader/>
      </template>
    </suspense>
    <suspense v-if="brandInfo.id">
      <products-by-brand :brand-slug="route.params.brandSlug" :brand-id="brandInfo.id"/>
      <template #fallback>
        <suspense-loader/>
      </template>
    </suspense>
  </main>
</template>

<style scoped>

</style>