<script setup>
import DiscountInfoViewer from "@/management/components/Discounts/DiscountInfoViewer.vue";
import DiscountForm from "@/management/components/Discounts/DiscountForm.vue";
import {useToastStore} from "@/stores/useToastStore.js";
import {formatInTimeZone} from "date-fns-tz";
import {WarehouseService} from "@/services/Product/WarehouseService.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import {ref} from "vue";
import DiscountCancelModal from "@/management/components/Discounts/DiscountCancelModal.vue";

const props = defineProps({
  discountData: Object | null,
  originalPrice: Number,
  productId: Number | String,
});

const toast = useToastStore();

const isEditing = ref(false);

const modalOpen = ref(false);

const emit = defineEmits(["discount-added"]);

const onFormSubmit = async (values) => {
  const date = {
    percentage: values.percentage,
    variation: null,
    start_date: prepareStartDate(values.start_date),
    end_date: prepareEndDate(values.end_date)
  };

  await WarehouseService.addDiscount(props.productId, date)
      .then((response) => {
        if (response?.status === 200) {
          emit("discount-added");
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

  await WarehouseService.updateDiscount(props.productId, props.discountData?.id, date)
      .then((response) => {
        if (response?.status === 200) {
          emit("discount-added");
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

  await WarehouseService.cancelDiscount(props.productId, props.discountData?.id)
      .then((response) => {
        if (response?.status === 200) {
          emit("discount-added");
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
    variation: null,
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
    <div class="w-full max-w-2xl p-6 bg-white rounded-lg shadow-lg dark:bg-gray-800 dark:border-gray-700 space-y-4">
      <h5
          class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white text-center"
      >
        Discounts
      </h5>
      <div class="flex flex-col gap-4" v-if="discountData && !isEditing">
        <div class="flex justify-around items-center gap-14">
          <discount-info-viewer
              title="Percentage"
              :value="discountData.percentage"
              icon="pi-percentage"
          />
          <discount-info-viewer
              title="Old Price"
              :value="`$${discountData.old_price}`"
              icon="pi-arrow-up text-red-500"
          />
          <discount-info-viewer
              title="Discount Price"
              :value="`$${discountData.discount_price}`"
              icon="pi-arrow-down text-green-500"
          />
        </div>
        <div class="flex justify-around">
          <div class="p-4 rounded-md shadow-md flex flex-col items-center gap-y-2">
            <i class="pi pi-calendar-plus text-sm text-green-500"></i>
            <span class="text-sm font-semibold">{{
                formatDate(discountData.start_from)
              }}</span>
            <span class="text-sm">Start Date</span>
          </div>
          <div class="p-4 rounded-md shadow-md flex flex-col items-center gap-y-2">
            <i class="pi pi-calendar-minus text-sm text-red-500"></i>
            <span class="text-sm font-semibold">{{
                formatDate(discountData.end_at)
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
          v-else-if="!discountData && !isEditing"
          :original-price="originalPrice"
          @form-submitted="onFormSubmit"
      />
      <discount-form
          v-if="isEditing"
          :original-price="originalPrice"
          :exists-discount="discountData"
          @form-updated="onFormUpdate"
          @update-cancel="discardEditing"
      />
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