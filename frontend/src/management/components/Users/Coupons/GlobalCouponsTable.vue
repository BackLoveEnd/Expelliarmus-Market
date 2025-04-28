<script setup>
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import {computed, ref} from "vue";
import {ShopCouponService} from "@/services/User/ShopCouponService.js";

const page = ref(0);
const isFetching = ref(false);
const totalRecords = ref(0);
const rowOptions = [5, 10, 25, 50];
const coupons = ref([]);
const limit = ref(rowOptions[0]);

const offset = computed(() => page.value * limit.value);

const fetchBrands = async (pageIndex) => {
  isFetching.value = true;

  await ShopCouponService.getCoupons(`/users/coupons/global?limit=${limit.value}&offset=${pageIndex * limit.value}`)
      .then((response) => {
        if (response?.data.meta.total === 0) {
          coupons.value = [];
        } else {
          coupons.value = response?.data?.data?.map((item) => ({id: item.id, ...item.attributes})) || [];

          totalRecords.value = response?.data?.meta?.total || 0;
        }
      })
      .catch((error) => {
      })
      .finally(() => {
        isFetching.value = false;
      });
};

await fetchBrands(0);

const onPageChange = async (event) => {
  if (event.rows !== limit.value) {
    limit.value = event.rows;
    page.value = 0;
    await fetchBrands(0);
    return;
  }

  const nextPage = Math.floor(event.first / event.rows);

  if (nextPage !== page.value) {
    page.value = nextPage;
    await fetchBrands(nextPage);
  }
};
</script>

<template>
  <div
      class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6"
      v-if="coupons.length === 0"
  >
    <div class="mx-auto max-w-screen-sm text-center">
      <p
          class="mb-4 text-2xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white"
      >
        Coupons not found
      </p>
      <button
          class="w-32 h-10 px-1 bg-blue-500 rounded-md text-white"
      >
        Add Coupon
      </button>
    </div>
  </div>
  <DataTable
      v-else
      lazy
      :value="coupons"
      :loading="isFetching"
      paginator
      :totalRecords="totalRecords"
      :first="offset"
      :rows="limit"
      @page="onPageChange"
      :rowsPerPageOptions="rowOptions"
      dataKey="id"
  >
    <template #header>
      <button
          class="w-32 h-10 px-1 bg-blue-500 rounded-md text-white"
      >
        Add Coupon
      </button>
    </template>
    <Column field="id" header="ID" style="width: 5%; padding-left: 1rem"/>
    <Column field="coupon" header="Coupon Code" style="width: 20%; padding-left: 1rem">
      <template #body="slotProps">
        <span class="font-bold">{{ slotProps.data.coupon }}</span>
      </template>
    </Column>
    <Column field="discount" header="Discount %" style="width: 30%; padding-left: 1rem"/>
    <Column field="type" header="Type" style="width: 20%">
      <template #body="slotProps">
        <span class="text-blue-500">{{ slotProps.data.type }}</span>
      </template>
    </Column>
    <Column field="expires_at" header="Expire Date" style="width: 20%; padding-left: 1rem">
      <template #body="slotProps">
        <span>{{ new Date(slotProps.data.expires_at).toLocaleDateString() }}</span>
      </template>
    </Column>
    <Column>
      <template #body="slotProps" style="width: 5%; min-width: 8rem">
        <button
            type="button"
            class="text-blue-500"
        >
          <i class="pi pi-file-arrow-up"></i>
        </button>
      </template>
    </Column>
    <Column>
      <template #body="slotProps" style="width: 5%; min-width: 8rem">
        <button
            type="button"
            class="text-red-500"
        >
          <i class="fa-solid fa-trash"></i>
        </button>
      </template>
    </Column>
  </DataTable>
</template>

<style scoped></style>
