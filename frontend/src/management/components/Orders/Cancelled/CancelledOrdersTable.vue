<template>
  <div class="card" v-if="userOrders.length > 0">
    <DataTable
        v-model:expandedRowGroups="expandedRowGroups"
        :value="userOrders"
        lazy
        @page="onPageChange"
        :rowsPerPageOptions="rowOptions"
        :loading="isFetching"
        paginator
        :totalRecords="totalRecords"
        :first="offset"
        :rows="limit"
        tableStyle="min-width: 60rem"
        expandableRowGroups
        rowGroupMode="subheader"
        groupRowsBy="user.id"
        :sortOrder="1"
    >
      <template #groupheader="slotProps">
        <span class="align-middle ml-2 font-bold leading-normal">
          {{ slotProps.data.user.full_name }} ({{ slotProps.data.user.type }})
        </span>
      </template>
      <Column field="user.first_name" header="First Name" style="text-align: center"></Column>
      <Column field="user.last_name" header="Last Name" style="text-align: center"></Column>
      <Column field="user.email" header="Email" style="text-align: center"></Column>
      <Column field="user.phone_number" header="Phone Number" style="text-align: center"></Column>
      <Column field="order.order_id" header="Order ID" style="text-align: center"></Column>
      <Column field="order.created_at" header="Created At" style="text-align: center">
        <template #body="slotProps">
          {{ new Date(slotProps.data.order.created_at).toLocaleString() }}
        </template>
      </Column>
      <Column field="order.status" header="Status" style="text-align: center">
        <template #body="slotProps">
          <Tag :value="slotProps.data.order.status" :severity="getSeverity(slotProps.data.order.status)"/>
        </template>
      </Column>
      <Column header="Order Lines" style="text-align: center">
        <template #body="slotProps">
          <button type="button" class="text-blue-500 hover:underline hover:underline-offset-4 decoration-blue-500">
            View
          </button>
        </template>
      </Column>
      <template #groupfooter="slotProps">
        <div class="flex justify-end font-bold w-full">
          Total Price: ${{ slotProps.data.order.total_price }}
        </div>
      </template>
      <Column field="" header="" style="display: none;"></Column>
    </DataTable>
  </div>
  <div v-if="isFetching === false && userOrders.length === 0">
    <main class="grid place-items-center">
      <div class="text-center">
        <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">Cancelled Orders not found.</p>
      </div>
    </main>
  </div>
</template>

<script setup>
import {ManagementOrdersService} from "@/services/Order/ManagementOrdersService.js";
import {computed, ref} from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";

const userOrders = ref([]);

const expandedRowGroups = ref(null);

const rowOptions = [20, 50, 100];

const limit = ref(rowOptions[0]);

const isFetching = ref(false);

const totalRecords = ref(0);

const page = ref(0);

const offset = computed(() => page.value * limit.value);

async function getPendingOrders(pageIndex) {
  isFetching.value = true;

  await ManagementOrdersService.getOrders(`/orders/cancelled?limit=${limit.value}&offset=${pageIndex * offset.value}`)
      .then((response) => {
        if (response?.data.meta.total === 0) {
          userOrders.value = [];
        } else {
          userOrders.value = response?.data?.data?.flatMap((item) => {
            const user = item.attributes.user;
            return item.attributes.orders.map((order) => ({
              user,
              order,
            }));
          });

          totalRecords.value = response?.data?.meta?.total || 0;
        }
      })
      .catch((e) => {

      })
      .finally(() => isFetching.value = false);
}

const onPageChange = async (event) => {
  if (event.rows !== limit.value) {
    limit.value = event.rows;
    page.value = 0;
    await getPendingOrders(0);
    return;
  }

  const nextPage = Math.floor(event.first / event.rows);

  if (nextPage !== page.value) {
    page.value = nextPage;
    await getPendingOrders(nextPage);
  }
};

const getSeverity = (status) => {
  switch (status) {
    case 'cancelled':
      return 'danger';
  }
};

await getPendingOrders(0);
</script>

<style>
.p-datatable-column-header-content {
  @apply justify-center;
}
</style>