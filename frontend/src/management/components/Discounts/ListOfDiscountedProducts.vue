<script setup>
import {reactive, ref} from "vue";
import MultiSelect from "primevue/multiselect";
import {WarehouseService} from "@/services/Product/WarehouseService.js";
import Select from "primevue/select";
import Tag from "primevue/tag";

let discountedProducts = reactive([]);

const statusFilter = ref([
  {value: 'cancelled', name: "Cancelled"},
  {value: 'finished', name: "Finished"},
  {value: 'active', name: "Active"},
  {value: 'pending', name: "Pending"}
]);

const finishedFilters = ref([
  {value: true, name: "Yes"},
  {value: false, name: "No"},
]);

const filters = ref({});

const isLoading = ref(false);

const selectedSorts = ref([]);

const nextPage = ref();

const sorts = ref([
  {value: 'start_date', name: 'By start date', direction: 'asc'},
  {value: 'end_date', name: 'By end date', direction: ''},
  {value: 'percentage', name: 'By percentage', direction: ''},
  {value: 'discount_price', name: 'By discount price', direction: ''}
]);

const total = ref(0);

const getDiscountedProducts = async (page = 1) => {
  const params = {
    filter: filters.value,
    sort: selectedSorts.value,
    page: page
  };

  isLoading.value = true;

  await WarehouseService.discountedProducts(params)
      .then((response) => {
        if (page === 1) {
          discountedProducts.splice(0, discountedProducts.length, ...response?.data?.data?.map((discount) => ({
            id: discount?.id,
            percentage: discount?.attributes?.discount?.percentage,
            cancelled: discount?.attributes?.discount?.cancelled,
            start_date: new Date(discount?.attributes?.discount?.start_date).toLocaleString(),
            end_date: new Date(discount?.attributes?.discount?.end_date).toLocaleString(),
            discount_price: discount?.attributes?.discount?.discount_price,
            status: discount?.attributes?.discount?.status,
            product: {
              id: discount?.attributes?.id,
              title: discount?.attributes?.title,
              article: discount?.attributes?.product_article,
              image: discount?.attributes?.image
            }
          })));
        } else {
          discountedProducts.push(...response?.data?.data?.map((discount) => ({
            id: discount?.id,
            percentage: discount?.attributes?.percentage,
            cancelled: discount?.attributes?.cancelled,
            start_date: new Date(discount?.attributes?.start_date).toLocaleString(),
            end_date: new Date(discount?.attributes?.end_date).toLocaleString(),
            discount_price: discount?.attributes?.discount_price,
            status: discount?.attributes?.status,
            product: {
              id: discount?.attributes?.product?.id,
              title: discount?.attributes?.product?.title,
              article: discount?.attributes?.product?.product_article,
              image: discount?.attributes?.product?.image
            }
          })));
        }

        total.value = response?.data?.meta.total;

        nextPage.value = response?.data?.links.next_page;
      })
      .catch((e) => {
        if (e.response?.status === 404) {
          discountedProducts.splice(0, discountedProducts.length);
          total.value = 0;
        }
      })
      .finally(() => isLoading.value = false);
};

async function loadMore() {
  await getDiscountedProducts(nextPage.value);
}

const onStatusFilter = async (selectedFilter) => {
  if (selectedFilter === null) {
    delete filters.value['status'];
  } else {
    filters.value['status'] = selectedFilter.value;
  }

  await getDiscountedProducts();
};

const onFinishedFilter = async (selectedFilter) => {
  if (selectedFilter === null) {
    delete filters.value['finished'];
  } else {
    filters.value['finished'] = selectedFilter.value;
  }

  await getDiscountedProducts();
};

const onSort = async (selectedFields) => {
  selectedSorts.value = selectedFields.map((field) => {
    const existingSort = selectedSorts.value.find((s) => s.field === field.value);
    return {
      field: field.value,
      order: existingSort ? existingSort.order : field.direction || "asc",
    };
  });

  await getDiscountedProducts();
};

const setSort = async (field, order) => {
  const existingSort = selectedSorts.value.find((sort) => sort.field === field);

  if (existingSort) {
    existingSort.order = order;

    await getDiscountedProducts();
  }
};

const isActiveSort = (field, order) => {
  return selectedSorts.value.some((sort) => sort.field === field && sort.order === order);
};

await getDiscountedProducts();

const truncatedTitle = (title) => {
  return title.length > 20 ? title.substring(0, 20) + "..." : title;
};

const getSeverity = (status) => {
  switch (status) {
    case "Cancelled":
      return "danger";
    case "Active":
      return "success";
    case "Pending":
      return "warn";
    case "Finished":
      return "secondary";
  }
};
</script>

<template>
  <section class="container mx-auto h-full">
    <div :class="{
          'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 place-items-center': discountedProducts.length > 0,
          'grid grid-cols-1 sm:grid-cols-2 gap-4 place-items-center': discountedProducts.length === 0
      }">
      <div class="flex flex-col justify-around h-full">
        <div class="flex gap-y-2 flex-col">
          <div class="text-lg font-semibold text-center">Total: {{ total }} products</div>
          <div class="text-sm text-center">Showing {{ discountedProducts.length }} products</div>
        </div>
        <div class="flex flex-col gap-y-4">
          <label class="text-center font-semibold">Filters & Sorting</label>

          <Select
              :options="statusFilter"
              placeholder="Status"
              style="min-width: 12rem"
              :showClear="true"
              option-label="name"
              @update:modelValue="onStatusFilter"
          >
            <template #option="{ option }">
              <span>{{ option.name }}</span>
            </template>
          </Select>

          <Select
              :options="finishedFilters"
              placeholder="Discount Finished"
              style="min-width: 12rem"
              :showClear="true"
              option-label="name"
              @update:modelValue="onFinishedFilter"
          >
            <template #option="{ option }">
              <span>{{ option.name }}</span>
            </template>
          </Select>

          <MultiSelect
              @update:modelValue="onSort"
              :options="sorts"
              optionLabel="name"
              filter
              placeholder="Select Sort Fields"
              display="chip"
              class="w-full md:w-80"
          >
            <template #option="{ option }">
              <div class="flex items-center justify-between w-full">
                <span>{{ option.name }}</span>
                <div class="flex gap-1 ml-2">
                  <button
                      @click.stop="setSort(option.value, 'asc')"
                      :class="{
                        'bg-yellow-500 text-white': isActiveSort(option.value, 'asc'),
                        'bg-gray-200': !isActiveSort(option.value, 'asc')
                      }"
                      class="px-2 py-1 rounded text-xs"
                  >
                    ASC
                  </button>
                  <button
                      @click.stop="setSort(option.value, 'desc')"
                      :class="{
                        'bg-blue-500 text-white': isActiveSort(option.value, 'desc'),
                        'bg-gray-200': !isActiveSort(option.value, 'desc')
                      }"
                      class="px-2 py-1 rounded text-xs"
                  >
                    DESC
                  </button>
                </div>
              </div>
            </template>
          </MultiSelect>
        </div>
      </div>

      <template v-if="discountedProducts.length > 0">
        <div v-for="(discount, index) in discountedProducts" :key="discount.id" class="max-w-[240px] my-8">
          <div class="bg-transparent rounded-lg overflow-hidden shadow-md">
            <div class="p-5 pb-0 overflow-hidden">
              <img :src="discount.product.image" alt="Product Image" class="w-full h-full object-cover rounded-lg"/>
            </div>
            <div class="p-5 space-y-2">
              <div class="text-sm text-center text-gray-900">
                {{ truncatedTitle(discount.product.title) }}
              </div>
              <div class="flex flex-col items-start mt-2 space-y-1">
                <span class="text-xs text-gray-500">Article: {{ discount.product.article }}</span>
                <span class="text-xs text-gray-500">
                  Status:
                  <Tag class="text-xs" :value="discount.status" :severity="getSeverity(discount.status)"/>
                </span>
                <span class="text-xs text-gray-500 font-semibold">Percentage: {{ discount.percentage }}</span>
                <span class="text-xs text-gray-500 font-semibold">Discount Price: {{ discount.discount_price }}</span>
                <span class="text-xs text-gray-500">Start At: {{ discount.start_date }}</span>
                <span class="text-xs text-gray-500">End At: {{ discount.end_date }}</span>
              </div>
            </div>
          </div>
        </div>
        <div v-if="discountedProducts.length < total">
          <button type="button" class="flex flex-col items-center gap-y-2" @click="loadMore">
            <i class="pi pi-sync text-4xl text-[#374151]" :class="{ 'pi-spin': isLoading }"></i>
            <span class="font-semibold text-[#374151]">Load more</span>
          </button>
        </div>
        <div v-else class="flex flex-col items-center gap-y-2">
          <i class="pi pi-check text-green-500 text-3xl"></i>
          <span class="text-[#374151] text-sm font-semibold">That all.</span>
        </div>
      </template>
      <div
          v-else
          class="no-file-found flex flex-col items-center justify-center py-8 px-4 text-center rounded-lg">
        <svg class="w-12 h-12 dark:text-gray-400 text-gray-700" stroke="currentColor" fill="currentColor"
             stroke-width="0" viewBox="0 0 24 24" height="200px" width="200px" xmlns="http://www.w3.org/2000/svg">
          <g id="File_Off">
            <g>
              <path
                  d="M4,3.308a.5.5,0,0,0-.7.71l.76.76v14.67a2.5,2.5,0,0,0,2.5,2.5H17.44a2.476,2.476,0,0,0,2.28-1.51l.28.28c.45.45,1.16-.26.7-.71Zm14.92,16.33a1.492,1.492,0,0,1-1.48,1.31H6.56a1.5,1.5,0,0,1-1.5-1.5V5.778Z"></path>
              <path
                  d="M13.38,3.088v2.92a2.5,2.5,0,0,0,2.5,2.5h3.07l-.01,6.7a.5.5,0,0,0,1,0V8.538a2.057,2.057,0,0,0-.75-1.47c-1.3-1.26-2.59-2.53-3.89-3.8a3.924,3.924,0,0,0-1.41-1.13,6.523,6.523,0,0,0-1.71-.06H6.81a.5.5,0,0,0,0,1Zm4.83,4.42H15.88a1.5,1.5,0,0,1-1.5-1.5V3.768Z"></path>
            </g>
          </g>
        </svg>
        <h3 class="text-xl font-medium mt-4 text-gray-700 dark:text-gray-200">Products not found.</h3>
        <p class="text-gray-500 dark:text-gray-400 mt-2">
          Products on discount you are looking for not found.
        </p>
      </div>
    </div>
  </section>
</template>

<style>
.p-select {
  @apply bg-white
}

.p-multiselect-label.p-placeholder {
  @apply text-[#374151] font-medium;
}
</style>
