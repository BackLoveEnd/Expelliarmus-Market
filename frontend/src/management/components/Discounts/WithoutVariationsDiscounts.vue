<script setup>
import DiscountInfoViewer from "@/management/components/Discounts/DiscountInfoViewer.vue";
import DiscountForm from "@/management/components/Discounts/DiscountForm.vue";
import {useToastStore} from "@/stores/useToastStore.js";
import {formatInTimeZone} from "date-fns-tz";
import {WarehouseService} from "@/services/WarehouseService.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";

const props = defineProps({
  discountData: Object | null,
  originalPrice: Number,
  productId: Number | String,
});

const toast = useToastStore();

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
        if (e?.response?.status === 422 || e?.response?.status === 404) {
          toast.showToast(e?.response?.data?.message, defaultErrorSettings);
        } else {
          toast.showToast("Unknown error. Try again or contact us.", defaultErrorSettings);
        }
      });
};

function prepareStartDate(startDate) {
  return formatInTimeZone(startDate, "UTC", "yyyy-MM-dd HH:mm");
}

function prepareEndDate(endDate) {
  return formatInTimeZone(endDate, "UTC", "yyyy-MM-dd HH:mm");
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
      <div class="flex flex-col gap-2" v-if="discountData">
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
            <span class="text-sm font-semibold">{{ discountData.start_from }}</span>
            <span class="text-sm">Start Date</span>
          </div>
          <div class="p-4 rounded-md shadow-md flex flex-col items-center gap-y-2">
            <i class="pi pi-calendar-minus text-sm text-red-500"></i>
            <span class="text-sm font-semibold">{{ discountData.end_at }}</span>
            <span class="text-sm">End Date</span>
          </div>
        </div>
      </div>
      <discount-form v-else :original-price="originalPrice" @form-submitted="onFormSubmit"/>
    </div>
  </section>
</template>

<style scoped>

</style>