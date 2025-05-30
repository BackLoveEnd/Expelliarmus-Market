<template>
  <div class="card">
    <DataTable
        :value="products"
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
        <div class="flex justify-between">
          <div class="flex gap-x-4">
            <div class="flex items-center gap-x-4">
              <span>Product Status:</span>
              <Select
                  :options="statuses"
                  placeholder="Select One"
                  style="min-width: 12rem"
                  :showClear="true"
                  option-label="name"
                  @update:modelValue="onStatusFilter"
              >
                <template #option="{ option }">
                  <Tag
                      :value="option.name"
                      :severity="getProductStatusSeverity(option.name)"
                  />
                </template>
              </Select>
            </div>
            <div class="flex items-center gap-x-4">
              <span>Warehouse status:</span>
              <Select
                  :options="warehouseStatuses"
                  placeholder="Select One"
                  style="min-width: 12rem"
                  :showClear="true"
                  option-label="name"
                  @update:modelValue="onWarehouseStatusFilter"
              >
                <template #option="{ option }">
                  <Tag
                      :value="option.name"
                      :severity="getWarehouseStatusSeverity(option.name)"
                  />
                </template>
              </Select>
            </div>
            <div class="flex items-center gap-x-4">
              <span>In Stock:</span>
              <Select
                  :options="inStockFilters"
                  placeholder="Select One"
                  style="min-width: 12rem"
                  :showClear="true"
                  option-label="name"
                  @update:modelValue="onInStockFilter"
              >
                <template #option="{ option }">
                  <span>{{ option.name }}</span>
                </template>
              </Select>
            </div>
          </div>
          <div>
            <h2 class="text-xl font-semibold text-center">Products Table</h2>
          </div>
        </div>
      </template>
      <template #empty>
        <div class="pl-4 text-center font-semibold">No products found.</div>
      </template>
      <template #loading> Loading products data. Please wait.</template>
      <Column field="title" header="Title" style="min-width: 12rem" sortable>
        <template #body="{ data }">{{ data.title }}</template>
      </Column>
      <Column header="Article" style="min-width: 12rem">
        <template #body="{ data }">{{ data.article }}</template>
      </Column>
      <Column
          field="total_quantity"
          header="Total Quantity"
          style="min-width: 14rem"
          sortable
      >
        <template #body="{ data }">{{ data.totalQuantity }}</template>
      </Column>
      <Column
          field="status"
          header="Status"
          :filter="true"
          style="min-width: 12rem"
      >
        <template #body="{ data }">
          <Tag
              :value="data.status.name"
              :severity="getProductStatusSeverity(data.status.name)"
          />
        </template>
      </Column>
      <Column
          field="warehouse_status"
          header="Warehouse Status"
          :filter="true"
          style="min-width: 12rem"
      >
        <template #body="{ data }">
          <Tag
              :value="data.warehouse_status.name"
              :severity="getWarehouseStatusSeverity(data.warehouse_status.name)"
          />
        </template>
      </Column>
      <Column
          field="arrived_at"
          header="Arrived Time"
          style="min-width: 6rem"
          sortable
      >
        <template #body="{ data }">{{ data.arrived_at }}</template>
      </Column>
    </DataTable>
  </div>
</template>

<script setup>
import {computed, reactive, ref} from "vue";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Tag from "primevue/tag";
import Select from "primevue/select";
import {WarehouseService} from "@/services/Product/WarehouseService.js";

const products = ref([]);
const totalRecords = ref(0);
const loading = ref(true);

const statuses = ref([
  {id: 1, name: "Not Published"},
  {id: 0, name: "Published"},
  {id: 2, name: "Trashed"},
]);

const warehouseStatuses = ref([
  {id: 0, name: "In Stock"},
  {id: 1, name: "Pending"},
  {id: 2, name: "Not Available"},
  {id: 3, name: "Partially"},
]);

const page = ref(0);
const limit = ref(10);
const cache = reactive(new Map());
const sortField = ref(null);
const sortOrder = ref(null);
const filters = ref({});
const rowOptions = [10, 25, 50];

const inStockFilters = ref([
  {value: true, name: "Yes"},
  {value: false, name: "No"},
]);


const offset = computed(() => page.value * limit.value);

async function fetchProducts(pageIndex) {
  if (cache.has(pageIndex)) {
    products.value = cache.get(pageIndex);
    return;
  }

  loading.value = true;

  const params = {
    limit: limit.value,
    offset: offset.value,
    sort: sortField.value
        ? {field: sortField.value, order: sortOrder.value}
        : null,
    filter: filters.value ?? null,
  };

  await WarehouseService.getProductsTable(params)
      .then((response) => {
        if (response?.data.meta.total === 0) {
        } else {
          products.value =
              response.data.data?.map((item) => item.attributes || {}) || [];
          totalRecords.value = response.data.meta.total;
          cache.set(pageIndex, products.value);
        }
      })
      .catch(() => {
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
    await fetchProducts(0);
    return;
  }

  const nextPage = Math.floor(event.first / event.rows);

  if (nextPage !== page.value) {
    page.value = nextPage;
    await fetchProducts(nextPage);
  }
};

const onSort = async (event) => {
  sortField.value = event.sortField;
  sortOrder.value = event.sortOrder === 1 ? "asc" : "desc";
  cache.clear();
  await fetchProducts(0);
};

const onStatusFilter = async (selectedStatus) => {
  if (selectedStatus === null) {
    delete filters.value["status"];
  } else {
    filters.value["status"] = selectedStatus.id;
  }
  cache.clear();
  await fetchProducts(0);
};

const onWarehouseStatusFilter = async (selectedStatus) => {
  if (selectedStatus === null) {
    delete filters.value["warehouse_status"];
  } else {
    filters.value["warehouse_status"] = selectedStatus.id;
  }
  cache.clear();
  await fetchProducts(0);
};

const onInStockFilter = async (selectedStock) => {
  if (selectedStock === null) {
    delete filters.value["in_stock"];
  } else {
    filters.value["in_stock"] = selectedStock.value;
  }
  cache.clear();
  await fetchProducts(0);
};

const getProductStatusSeverity = (status) => {
  switch (status) {
    case "Not Published":
      return "warn";
    case "Published":
      return "success";
    case "Trashed":
      return "danger";
  }
};

const getWarehouseStatusSeverity = (status) => {
  switch (status) {
    case "In Stock":
      return "success";
    case "Pending":
      return "warn";
    case "Not Available":
      return "danger";
    case "Partially":
      return "info";
  }
};

await fetchProducts(0);
</script>
