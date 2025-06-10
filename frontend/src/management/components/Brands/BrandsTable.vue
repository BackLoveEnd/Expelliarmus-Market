<script setup>
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import InputText from "primevue/inputtext";
import {computed, reactive, ref} from "vue";
import {BrandsService} from "@/services/Brand/BrandsService.js";
import AddNewBrand from "@/management/components/Brands/AddNewBrand.vue";
import {useToastStore} from "@/stores/useToastStore.js";
import successCategorySettings from "@/management/components/Toasts/Category/successCategorySettings.js";
import failedCategorySettings from "@/management/components/Toasts/Category/failedCategorySettings.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import DeleteBrand from "@/management/components/Brands/DeleteBrand.vue";
import EditBrandImageModal from "@/management/components/Brands/EditBrandImageModal.vue";

const page = ref(0);
const isFetching = ref(false);
const totalRecords = ref(0);
const rowOptions = [5, 10, 25, 50];
const brands = ref([]);
const limit = ref(rowOptions[0]);
const editingRows = ref([]);
const isAddNewBrandModalOpen = ref(false);
const isDeleteBrandModalOpen = ref(false);
const isEditBrandImageModalOpen = ref(false);
const toast = useToastStore();
const brandsEmpty = ref(true);

let deleteBrandData = reactive({});

let editBrandImageData = reactive({});

const cache = reactive(new Map());

const offset = computed(() => page.value * limit.value);

const fetchBrands = async (pageIndex) => {
  if (cache.has(pageIndex)) {
    brands.value = cache.get(pageIndex);
    return Promise.resolve();
  }

  isFetching.value = true;

  return await BrandsService.fetchBrands(
      `/management/brands?limit=${limit.value}&offset=${pageIndex * limit.value}`,
  )
      .then((response) => {
        if (response?.data.meta.total === 0) {
          brandsEmpty.value = true;
        } else {
          brands.value =
              response?.data?.data?.map((item) => item.attributes || {}) || [];
          cache.set(pageIndex, brands.value);
          totalRecords.value = response?.data?.meta?.total || 0;
          brandsEmpty.value = false;
        }
      })
      .catch((error) => {
      })
      .finally(() => {
        isFetching.value = false;
      });
};

const openAddNewBrandModal = () => {
  isAddNewBrandModalOpen.value = true;
};

const openDeleteBrandModal = (props) => {
  isDeleteBrandModalOpen.value = true;
  deleteBrandData = props.data;
};

const openEditBrandImageModal = (props) => {
  isEditBrandImageModalOpen.value = true;
  editBrandImageData = props.data;
  console.log(props.data);
};

await fetchBrands(0);

const onPageChange = async (event) => {
  if (event.rows !== limit.value) {
    limit.value = event.rows;
    cache.clear();
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

const brandDeleted = async (brandId) => {
  brands.value = brands.value.filter((brand) => brand.id !== brandId);
  totalRecords.value -= 1;
  cache.clear();
  if (brands.value.length === 0) {
    page.value = page.value !== 0 ? page.value - 1 : page.value;
    await fetchBrands(page.value);
  }
};

const onRowEditSave = async (event) => {
  let {newData, index} = event;

  await BrandsService.editCategory(newData)
      .then((response) => {
        if (response?.data?.data.slug) {
          newData.slug = response.data.data.slug;
          brands.value[index] = newData;
          cache.set(page.value, [...brands.value]);
          toast.showToast(response.data.message, successCategorySettings);
        } else {
          toast.showToast(
              "Brand information is up-to-date.",
              successCategorySettings,
          );
        }
      })
      .catch((e) => {
        if (e.response?.status === 404) {
          toast.showToast("Brand not found", failedCategorySettings);
        } else if (e.response?.status === 422) {
          const errors = useJsonApiFormatter().fromJsonApiErrorsFields(
              e.response.data.errors,
          );

          toast.showToast(Object.values(errors[0])[0], failedCategorySettings);
        } else {
          toast.showToast("Failed to update brand. Try again or contact us.");
        }
      });
};

const addBrandToTable = (brand) => {
  brands.value = [...brands.value, brand];
  brandsEmpty.value = false;
};
</script>

<template>
  <div
      class="py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6"
      v-if="brandsEmpty"
  >
    <div class="mx-auto max-w-screen-sm text-center">
      <p
          class="mb-4 text-2xl tracking-tight font-bold text-gray-900 md:text-4xl dark:text-white"
      >
        Brands not found
      </p>
      <button
          @click="openAddNewBrandModal"
          class="w-32 h-10 px-1 bg-blue-500 rounded-md text-white"
      >
        Add Brand
      </button>
    </div>
  </div>
  <DataTable
      v-else
      lazy
      :value="brands"
      :loading="isFetching"
      paginator
      :totalRecords="totalRecords"
      :first="offset"
      :rows="limit"
      :rowsPerPageOptions="rowOptions"
      @page="onPageChange"
      v-model:editingRows="editingRows"
      editMode="row"
      dataKey="id"
      @row-edit-save="onRowEditSave"
  >
    <template #header>
      <button
          @click="openAddNewBrandModal"
          class="w-32 h-10 px-1 bg-blue-500 rounded-md text-white"
      >
        Add Brand
      </button>
    </template>
    <Column field="id" header="ID" style="width: 5%"/>
    <Column field="brand_name" header="Brand Name" style="width: 20%">
      <template #body="slotProps">
        <span class="font-bold">{{ slotProps.data.brand_name }}</span>
      </template>
      <template #editor="{ data, field }">
        <InputText v-model="data[field]" fluid/>
      </template>
    </Column>
    <Column field="description" header="Description" style="width: 30%">
      <template #body="slotProps">
        <span>
          {{
            slotProps.data.description && slotProps.data.description.length > 50
                ? slotProps.data.description.slice(0, 40) + "..."
                : slotProps.data.description || ""
          }}
        </span>
      </template>
      <template #editor="{ data, field }">
        <InputText v-model="data[field]" fluid/>
      </template>
    </Column>
    <Column field="slug" header="Slug" style="width: 20%"/>
    <Column
        :rowEditor="true"
        style="width: 5%; min-width: 8rem"
        bodyStyle="text-align:center"
    />
    <Column>
      <template #body="slotProps" style="width: 5%; min-width: 8rem">
        <button
            @click="openEditBrandImageModal(slotProps)"
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
            @click="openDeleteBrandModal(slotProps)"
            type="button"
            class="text-red-500"
        >
          <i class="fa-solid fa-trash"></i>
        </button>
      </template>
    </Column>
  </DataTable>

  <add-new-brand
      :isModalOpen="isAddNewBrandModalOpen"
      @modal-close="isAddNewBrandModalOpen = false"
      @brand-added="addBrandToTable"
  />

  <edit-brand-image-modal
      :is-modal-open="isEditBrandImageModalOpen"
      :edit-brand-image-data="editBrandImageData"
      @modal-close="isEditBrandImageModalOpen = false"
  />

  <delete-brand
      :is-modal-open="isDeleteBrandModalOpen"
      @modal-close="isDeleteBrandModalOpen = false"
      :brand-data="deleteBrandData"
      @brand-deleted="brandDeleted"
  />
</template>

<style scoped></style>
