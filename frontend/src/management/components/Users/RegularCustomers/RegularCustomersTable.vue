<template>
  <div class="card">
    <DataTable
        :value="users"
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
            <h2 class="text-xl font-semibold text-center">Regular Customers Table</h2>
          </div>
        </div>
      </template>
      <template #empty>
        <div class="pl-4 text-center font-semibold">No users found.</div>
      </template>
      <template #loading>Loading users data. Please wait.</template>
      <Column header="User ID" style="min-width: 12rem">
        <template #body="{ data }">
          <button v-tooltip.bottom="'Copy id'" @click="copyToClipboard(data.user_id_original, data.user_id_original)">
            {{ data.user_id }}
            <span v-if="copiedState.get(data.user_id_original) ?? false" class="text-xs text-green-500">Copied!</span>
          </button>
        </template>
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
      <Column field="phone_mask" header="Phone Number" style="min-width: 12rem">
        <template #body="{ data }">
          <button v-tooltip.bottom="'Copy number'"
                  @click="copyToClipboard(data.phone_original, `phone_${data.user_id_original}`)">
            {{ data.phone_mask }}
            <span v-if="copiedState.get(`phone_${data.user_id_original}`) ?? false" class="text-xs text-green-500">Copied!</span>
          </button>
        </template>
      </Column>
      <Column
          field="created_at"
          header="Registration Time"
          style="min-width: 6rem"
          sortable
      >
        <template #body="{ data }">{{ data.created_at }}</template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup>
import {computed, reactive, ref} from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import {RegularCustomersService} from "@/services/User/RegularCustomersService.js";
import {useClipboard} from "@vueuse/core";

const users = ref([]);
const totalRecords = ref(0);
const loading = ref(true);
const page = ref(0);
const limit = ref(10);
const cache = reactive(new Map());
const sortField = ref(null);
const sortOrder = ref(null);
const rowOptions = [10, 25, 50];

const {text, copy, copied, isSupported} = useClipboard({legacy: true});
const copiedState = reactive(new Map());

const offset = computed(() => page.value * limit.value);

async function fetchUsers(pageIndex) {
  if (cache.has(pageIndex)) {
    users.value = cache.get(pageIndex);
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

  await RegularCustomersService.getUsers(params)
      .then((response) => {
        if (response?.data.meta.total === 0) {
          users.value = response;
        } else {
          users.value = response.data.data?.map((item) => ({
            user_id_original: item.id,
            ...item.attributes
          })) || [];
          totalRecords.value = response.data.meta.total;
          cache.set(pageIndex, users.value);
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
    await fetchUsers(0);
    return;
  }

  const nextPage = Math.floor(event.first / event.rows);

  if (nextPage !== page.value) {
    page.value = nextPage;
    await fetchUsers(nextPage);
  }
};

const copyToClipboard = (original, userId) => {
  copy(original);

  copiedState.set(userId, true);

  setTimeout(() => {
    copiedState.delete(userId);
  }, 2000);
};

const onSort = async (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder === 1 ? "asc" : "desc";
  cache.clear();
  await fetchUsers(0);
};

await fetchUsers(0);
</script>
