<script setup>
import {ref} from "vue";
import Select from "primevue/select";
import DiscountInfoViewer from "@/management/components/Discounts/DiscountInfoViewer.vue";
import DiscountForm from "@/management/components/Discounts/DiscountForm.vue";
import {WarehouseService} from "@/services/Product/WarehouseService.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import {formatInTimeZone} from "date-fns-tz";
import {useToastStore} from "@/stores/useToastStore.js";
import DiscountCancelModal from "@/management/components/Discounts/DiscountCancelModal.vue";

const props = defineProps({
  variations: Array,
  productId: Number | String
});

const toast = useToastStore();

const selectedVariation = ref(null);

const emit = defineEmits(["discount-added"]);

const isEditing = ref(false);

const modalOpen = ref(false);

const onFormSubmit = async (values) => {
  const date = prepareDate(values);

  await WarehouseService.addDiscount(props.productId, date)
      .then((response) => {
        if (response?.status === 200) {
          emit("discount-added");
          selectedVariation.value = null;
          toast.showToast(response?.data?.message, defaultSuccessSettings);
        }
      })
      .catch((e) => {
        if ([422, 404].includes(e?.response?.status)) {
          toast.showToast(e?.response?.data?.message, defaultErrorSettings);
        } else {
          toast.showToast("Unknown error. Try again or contact us.", defaultErrorSettings);
        }
      });
};

const onFormUpdate = async (values) => {
  const date = prepareDate(values);

  await WarehouseService.updateDiscount(props.productId, selectedVariation.value?.discount?.id, date)
      .then((response) => {
        if (response?.status === 200) {
          emit("discount-added");
          selectedVariation.value = null;
          toast.showToast(response?.data?.message, defaultSuccessSettings);
        }
      })
      .catch((e) => {
        if ([400, 409, 422, 404].includes(e?.response?.status)) {
          toast.showToast(e?.response?.data?.message, defaultErrorSettings);
        } else {
          toast.showToast("Unknown error. Try again or contact us.", defaultErrorSettings);
        }
      })
      .finally(() => isEditing.value = false);
};

const onCancelDiscount = async () => {
  modalOpen.value = false;

  await WarehouseService.cancelDiscount(props.productId, selectedVariation.value?.discount?.id)
      .then((response) => {
        if (response?.status === 200) {
          emit("discount-added");
          selectedVariation.value = null;
          toast.showToast(response?.data?.message, defaultSuccessSettings);
        }
      })
      .catch((e) => {
        if ([409, 422, 404].includes(e?.response?.status)) {
          toast.showToast(e?.response?.data?.message, defaultErrorSettings);
        } else {
          toast.showToast("Unknown error. Try again or contact us.", defaultErrorSettings);
        }
      });
};

function prepareDate(values) {
  return {
    percentage: values.percentage,
    variation: selectedVariation.value.id,
    start_date: values.start_date ? prepareStartDate(values.start_date) : null,
    end_date: prepareEndDate(values.end_date)
  };
}

function prepareStartDate(startDate) {
  return formatInTimeZone(startDate, "UTC", "yyyy-MM-dd HH:mm");
}

function prepareEndDate(endDate) {
  return formatInTimeZone(endDate, "UTC", "yyyy-MM-dd HH:mm");
}

const discardEditing = () => {
  isEditing.value = false;
};

const editDiscount = () => {
  isEditing.value = true;
};

function formatDate(date) {
  return new Date(date).toLocaleString();
}
</script>

<template>
  <section class="container mx-auto flex flex-col items-center gap-y-8">
    <Select
        :options="variations"
        style="width: 50%"
        placeholder="Choose product option"
        :showClear="true"
        option-label="attribute_name"
        v-model="selectedVariation"
    >
      <template #option="{ option }">
        <div class="flex gap-x-2 items-center">
          <span>{{ option.attribute_name }}</span>
          <span class="text-red-500" v-if="option?.discount">(On Sale Now)</span>
        </div>
      </template>
    </Select>
    <div
        v-if="selectedVariation"
        class="w-full max-w-2xl p-6 bg-white rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700 space-y-4"
    >
      <h5
          class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"
      >
        Option - {{ selectedVariation.attribute_name }}
      </h5>
      <div class="flex flex-col gap-y-4 text-sm">
        <div class="flex gap-x-2">
          <div
              class="flex gap-x-2 mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
              v-if="selectedVariation.attribute_type === 'color'"
          >
            <p> Value: </p>
            <input
                type="color"
                class="w-8 h-auto"
                :value="selectedVariation.value"
                disabled
            />
          </div>
          <div
              class="flex gap-x-2 mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
              v-else
          >
            <p> Value: </p>
            <p>{{ selectedVariation.value }}</p>
          </div>
        </div>
        <div class="flex justify-between">
          <p class="font-semibold">Price: {{
            "$" + (selectedVariation?.discount?.discount_price ?? selectedVariation.price)
            }}</p>
          <p class="font-semibold">
            Total quantity: {{ selectedVariation.quantity }} unit(s)
          </p>
        </div>
      </div>
      <div>
        <h5
            class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white text-center"
        >
          Discounts
        </h5>
        <div class="flex flex-col gap-4" v-if="selectedVariation.discount && !isEditing">
          <div class="flex justify-around items-center gap-14">
            <discount-info-viewer
                title="Percentage"
                :value="selectedVariation.discount.percentage"
                icon="pi-percentage"
            />
            <discount-info-viewer
                title="Old Price"
                :value="`$${selectedVariation.discount.old_price}`"
                icon="pi-arrow-up text-red-500"
            />
            <discount-info-viewer
                title="Discount Price"
                :value="`$${selectedVariation.discount.discount_price}`"
                icon="pi-arrow-down text-green-500"
            />
          </div>
          <div class="flex justify-around">
            <div class="p-4 rounded-md shadow-md flex flex-col items-center gap-y-2">
              <i class="pi pi-calendar-plus text-sm text-green-500"></i>
              <span class="text-sm font-semibold">{{
                  formatDate(selectedVariation.discount.start_from)
                }}</span>
              <span class="text-sm">Start Date</span>
            </div>
            <div class="p-4 rounded-md shadow-md flex flex-col items-center gap-y-2">
              <i class="pi pi-calendar-minus text-sm text-red-500"></i>
              <span class="text-sm font-semibold">{{
                  formatDate(selectedVariation.discount.end_at)
                }}</span>
              <span class="text-sm">End Date</span>
            </div>
          </div>
          <div class="flex justify-end gap-4">
            <button
                type="button"
                class="p-2 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-800"
                @click="editDiscount"
            >
              Edit Discount
            </button>
            <button
                @click="modalOpen = true"
                type="button"
                class="p-2 bg-red-500 text-white text-sm rounded-md hover:bg-red-800">
              Cancel Discount
            </button>
          </div>
        </div>
        <discount-form
            v-else-if="!selectedVariation.discount && !isEditing"
            :original-price="selectedVariation.price"
            @form-submitted="onFormSubmit"
        />
        <discount-form
            v-if="isEditing"
            :original-price="selectedVariation.price"
            :exists-discount="selectedVariation.discount"
            @form-updated="onFormUpdate"
            @update-cancel="discardEditing"
        />
      </div>
    </div>
  </section>
  <discount-cancel-modal
      :is-modal-open="modalOpen"
      @modal-closed="modalOpen = false"
      @cancel-approved="onCancelDiscount"
  />
</template>

<style scoped>

</style>