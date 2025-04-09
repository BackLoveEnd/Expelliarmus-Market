<script setup>
import {onMounted, ref} from "vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import {BrandsService} from "@/services/Brand/BrandsService.js";
import {useScrolling} from "@/composables/useScrolling.js";
import {useTruncator} from "@/composables/useTruncator.js";

const brands = ref([]);

const isLoading = ref(true);

const truncator = useTruncator();

const total = ref(0);

const nextPageUrl = ref(null);

async function getBrands(url = '/shop/brands/browse-list?page=1') {
  isLoading.value = true;

  await BrandsService.fetchBrands(url)
      .then((response) => {
        brands.value.push(...response?.data?.data?.map((brand) => ({...brand.attributes, id: brand.id})));

        total.value = response?.data?.meta?.total ?? 0;

        nextPageUrl.value = response?.data?.links?.next ?? null;
      })
      .catch((e) => {
      })
      .finally(() => isLoading.value = false);
}

async function loadMore() {
  if (nextPageUrl.value) {
    await getBrands(nextPageUrl.value);
  }
}

onMounted(async () => await getBrands());
</script>

<template>
  <main class="container mx-auto">
    <section class="py-8 antialiased md:py-16" v-if="brands.length > 0">
      <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
        <div class="mb-4 flex items-center justify-between gap-4 md:mb-8">
          <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Shop by brands</h2>
        </div>
        <suspense-loader v-if="isLoading"/>
        <div class="grid gap-8 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4" v-else>
          <router-link
              v-for="(brand, index) in brands"
              @click.prevent="useScrolling().scrollToTop()"
              :to="{ name: 'brands-browse', params: { brandSlug: brand.slug } }"
              class="card w-48 h-40 border-2 border-gray-300 rounded-md flex flex-col justify-center items-center gap-y-4 transition-all duration-300"
          >
            <div class="flex items-center justify-center">
              <img
                  :src="brand.logo"
                  :alt="brand.brand_name"
                  class="brand-logo max-h-24 max-w-full object-contain"
              />
            </div>
            <div>
              <span class="text-sm text-center">{{ truncator.truncateString(brand.brand_name, 15) }}</span>
            </div>
          </router-link>
        </div>
        <div class="flex justify-center my-6" v-if="total > brands.length">
          <router-link
              @click="loadMore"
              :to="{ name: 'brands-overview' }"
              class="text-black text-sm underline underline-offset-2 text-center"
          >
            Show More
          </router-link>
        </div>
      </div>
    </section>
    <section v-if="total === 0 && !isLoading">
      <div class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
        <div class="mx-auto max-w-screen-sm text-center">
          <p
              class="mb-4 text-3xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white"
          >
            Brands not found
          </p>
        </div>
      </div>
    </section>

    <suspense-loader v-if="isLoading"/>
  </main>
</template>

<style scoped>
.card:hover {
  @apply border-gray-500;
}
</style>