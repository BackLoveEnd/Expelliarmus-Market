<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {useToastStore} from "@/stores/useToastStore.js";
import {ShopCouponService} from "@/services/User/ShopCouponService.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";

const props = defineProps({
  isModalOpen: Boolean,
  couponId: String
});

const toast = useToastStore();

const emit = defineEmits(["modal-close", "coupon-deleted"]);

const closeModal = () => {
  emit("modal-close");
};

async function deleteCoupon() {
  await ShopCouponService.deleteCoupon(props.couponId)
      .then((response) => {
        if (response?.status === 200) {
          closeModal();

          emit("coupon-deleted");

          toast.showToast('Coupon deleted successfully', defaultSuccessSettings);
        }
      })
      .catch((e) => {
        toast.showToast('Error deleting coupon', defaultErrorSettings);
      });
}
</script>

<template>
  <DefaultModal
      :isActive="isModalOpen"
      @close-modal="closeModal"
      max-width="max-w-2xl"
  >
    <template #modalHeading>Delete Coupon</template>

    <template #modalBody>
      <div class="flex flex-col items-center gap-y-4">
        <span>Are you sure you want to delete coupon:</span>
        <span class="font-semibold">{{ couponId }}</span>
      </div>
    </template>

    <template #modalFooter>
      <div class="flex justify-between">
        <button
            type="button"
            @click="deleteCoupon"
            class="px-4 py-2 bg-red-500 text-white rounded-md"
        >
          Delete
        </button>
        <button
            type="button"
            @click="closeModal"
            class="ml-2 px-4 py-2 bg-gray-300 rounded-md"
        >
          Cancel
        </button>
      </div>
    </template>
  </DefaultModal>
</template>

<style scoped></style>
