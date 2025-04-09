<script setup>
import BreadCrumbs from "@/components/Default/BreadCrumbs.vue";
import {useBreadCrumbStore} from "@/stores/useBreadCrumbStore.js";
import {BrandsService} from "@/services/Brand/BrandsService.js";
import {reactive} from "vue";

const props = defineProps({
  brandSlug: String,
});

let brandInfo = reactive({});

const breadCrumbStore = useBreadCrumbStore();

const emit = defineEmits(["brand-info"]);

async function getBrand() {
  await BrandsService.getBrandInfo(props.brandSlug)
      .then((response) => {
        brandInfo = ({...response?.data?.data?.attributes, id: response?.data?.data?.id});

        emit("brand-info", brandInfo);

        breadCrumbStore.addBreadcrumb({
              name: brandInfo.brand_name, url: `/shop/brands/${brandInfo.slug}`
            }
        );
      })
      .catch((e) => {

      });
}

await getBrand();
</script>

<template>
  <section class="flex flex-col gap-y-8">
    <bread-crumbs :links="breadCrumbStore.breadcrumbs"/>
    <div class="flex flex-col gap-y-4">
      <div class="flex items-center">
        <img
            :src="brandInfo.logo_url"
            :alt="brandInfo.brand_name"
            class="brand-logo max-h-24 max-w-full object-contain"
        />
      </div>
      <span class="text-3xl font-semibold">{{ brandInfo.brand_name }}</span>
      <p class="text-sm max-w-2xl">{{ brandInfo.description }}</p>
    </div>
  </section>
</template>

<style scoped>

</style>