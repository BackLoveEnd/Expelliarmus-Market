<script setup>
import {StatisticsService} from "@/services/Different/StatisticsService.js";
import {onMounted, reactive, ref} from "vue";

const statistics = reactive({
  total_products: 0,
  total_users: 0,
  total_orders: 0
});

const isLoaded = ref(false);

async function getStatistics() {
  await StatisticsService.getStatisticForManagerHome()
      .then((response) => {
        const data = response?.data?.data?.attributes;

        statistics.total_products = data?.total_products || 0;

        statistics.total_users = data?.total_users || 0;

        statistics.total_orders = data?.total_orders || 0;
      })
      .catch((e) => {
        Object.assign(statistics, 0);
      })
      .finally(() => isLoaded.value = true);
}

onMounted(() => getStatistics());
</script>

<template>
  <div class="py-12">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <dl class="grid grid-cols-1 gap-x-8 gap-y-16 text-center lg:grid-cols-3">
        <div class="mx-auto flex max-w-xs flex-col gap-y-4">
          <dt class="text-base/7 text-gray-600">Total Products</dt>
          <dd class="order-first text-3xl text-center font-semibold tracking-tight text-gray-900 sm:text-5xl">
            <div role="status" class="max-w-sm animate-pulse" v-if="! isLoaded">
              <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-16 mx-auto mb-4"></div>
            </div>
            <span v-else>{{ statistics.total_products }}</span>
          </dd>
        </div>
        <div class="mx-auto flex max-w-xs flex-col gap-y-4">
          <dt class="text-base/7 text-gray-600">Completed Orders</dt>
          <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">
            <div role="status" class="max-w-sm animate-pulse" v-if="! isLoaded">
              <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-16 mx-auto mb-4"></div>
            </div>
            <span v-else>{{ statistics.total_orders }}</span>
          </dd>
        </div>
        <div class="mx-auto flex max-w-xs flex-col gap-y-4">
          <dt class="text-base/7 text-gray-600">Total Users</dt>
          <dd class="order-first text-3xl font-semibold tracking-tight text-gray-900 sm:text-5xl">
            <div role="status" class="max-w-sm animate-pulse" v-if="! isLoaded">
              <div class="h-2.5 bg-gray-300 rounded-full dark:bg-gray-700 w-16 mx-auto mb-4"></div>
              <span class="sr-only">Loading...</span>
            </div>
            <span v-else>{{ statistics.total_users }}</span>
          </dd>
        </div>
      </dl>
    </div>
  </div>
</template>

<style scoped>

</style>