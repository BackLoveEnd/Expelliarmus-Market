<template>
  <div class="card">
    <DataTable
        :value="managers"
        paginator
        :rows="limit"
        :first="offset"
        :totalRecords="totalRecords"
        :rowsPerPageOptions="rowOptions"
        :lazy="true"
        :loading="loading"
        @page="onPageChange"
        @sort="onSort"
        dataKey="id"
    >
      <template #header>
        <div class="flex justify-between items-center">
          <div>
            <h2 class="text-base font-semibold">Total: {{ totalRecords }}</h2>
          </div>
          <div>
            <h2 class="text-xl font-semibold text-center">Managers Table</h2>
          </div>
        </div>
      </template>
      <template #empty>
        <div class="pl-4 text-center font-semibold">No managers found.</div>
      </template>
      <template #loading>Loading managers data. Please wait.</template>
      <Column header="Manager ID" style="min-width: 12rem">
        <template #body="{ data }">{{ data.manager_id }}</template>
      </Column>
      <Column field="first_name" header="First Name" style="min-width: 12rem" sortable>
        <template #body="{ data }">{{ data.first_name }}</template>
      </Column>
      <Column
          field="last_name"
          header="Last Name"
          style="min-width: 14rem"
          sortable
      >
        <template #body="{ data }">{{ data.last_name }}</template>
      </Column>
      <Column field="email" header="Email">
        <template #body="{ data }">{{ data.email }}</template>
      </Column>
      <Column
          field="created_at"
          header="Registration Time"
          style="min-width: 6rem"
          sortable
      >
        <template #body="{ data }">{{ new Date(data.created_at).toLocaleString() }}</template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup>
import {computed, reactive, ref} from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import {ManagersService} from "@/services/Managers/ManagersService.js";

const managers = ref([]);
const totalRecords = ref(0);
const loading = ref(true);
const page = ref(0);
const limit = ref(10);
const cache = reactive(new Map());
const sortField = ref(null);
const sortOrder = ref(null);
const rowOptions = [10, 25, 50];

const offset = computed(() => page.value * limit.value);

async function fetchManagers(pageIndex) {
  if (cache.has(pageIndex)) {
    managers.value = cache.get(pageIndex);
    return;
  }

  loading.value = true;

  const params = {
    limit: limit.value,
    offset: offset.value,
    sort: sortField.value
        ? {field: sortField.value, order: sortOrder.value}
        : null,
  };

  await ManagersService.getManagers(params)
      .then((response) => {
        if (response?.data.meta.total === 0) {
          managers.value = response;
        } else {
          managers.value = response.data.data?.map((item) => ({
            user_id_original: item.id,
            ...item.attributes
          })) || [];
          totalRecords.value = response.data.meta.total;
          cache.set(pageIndex, managers.value);
        }
      })
      .catch((e) => {
      })
      .finally(() => {
        loading.value = false;
      });
}

const onPageChange = async (event) => {
  if (event.rows !== limit.value) {
    limit.value = event.rows;
    cache.clear();
    page.value = 0;
    await fetchManagers(0);
    return;
  }

  const nextPage = Math.floor(event.first / event.rows);

  if (nextPage !== page.value) {
    page.value = nextPage;
    await fetchManagers(nextPage);
  }
};

const onSort = async (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder === 1 ? "asc" : "desc";
  cache.clear();
  await fetchManagers(0);
};

await fetchManagers(0);
</script>
