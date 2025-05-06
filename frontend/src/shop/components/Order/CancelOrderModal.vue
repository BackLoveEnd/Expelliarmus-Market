<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {ShopOrderService} from "@/services/Order/ShopOrderService.js";

const props = defineProps({
  isOpen: Boolean,
  orderId: String | Number
});

const emit = defineEmits(["close-modal", "order-cancelled", "failed"]);

async function cancel() {
  await ShopOrderService.cancelOrder(props.orderId)
      .then((response) => {
        emit("order-cancelled");

        closeModal();
      })
      .catch((e) => {
        emit("failed");
      });
}

const closeModal = () => {
  emit("close-modal");
};
</script>

<template>
  <default-modal :is-active="isOpen" max-width="max-w-md" space-between-templates="space-y-6">
    <template #modalHeading>
      <span class="font-semibold">Cancelling Order № {{ orderId }}</span>
    </template>
    <template #modalBody>
      <div class="text-center w-full">
        <span class="text-center">Are you sure you want to cancel order?</span>
      </div>
    </template>
    <template #modalFooter>
      <div class="flex justify-between">
        <button type="button" @click="closeModal" class="py-2 px-4 bg-gray-500 text-white rounded-md z-50">
          Cancel
        </button>
        <button type="button" @click="cancel" class="py-2 px-4 bg-red-500 text-white rounded-md z-50">
          Yes
        </button>
      </div>
    </template>
  </default-modal>
</template>

<style scoped>

</style>