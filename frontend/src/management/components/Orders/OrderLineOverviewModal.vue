<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {ref, watch} from "vue";
import {ManagementOrdersService} from "@/services/Order/ManagementOrdersService.js";
import OrderLineProductCard from "@/management/components/Orders/OrderLineProductCard.vue";

const props = defineProps({
  isOpen: Boolean,
  orderId: Number | String,
  totalPrice: Number | String
});

const emit = defineEmits(["close-modal"]);

const orderLines = ref([]);

async function getOrderLines() {
  await ManagementOrdersService.getOrderLineForOrder(props.orderId)
      .then((response) => {
        orderLines.value = response?.data?.data?.map((line) => ({
          id: line.id,
          product: line.attributes.product,
          quantity: line.attributes.quantity,
          totalPrice: line.attributes.total_price,
          pricePerUnit: line.attributes.price_per_unit
        }));
      })
      .catch((e) => {

      });
}

function closeModal() {
  emit("close-modal");
}

watch(
    () => props.orderId,
    async (newValue) => {
      if (newValue !== null) {
        await getOrderLines();
      }
    },
    {immediate: true}
);
</script>

<template>
  <DefaultModal :is-active="isOpen" max-width="max-w-xl">
    <template #modalHeading>
      <span class="font-semibold">Order Lines for Order № {{ orderId }}</span>
    </template>
    <template #modalBody>
      <div v-if="orderLines.length > 0" class="space-x-4">
        <div>
          <order-line-product-card
              v-for="orderLine in orderLines"
              :product="{...orderLine.product, price_per_unit: orderLine.pricePerUnit}"
              :quantity="orderLine.quantity"
              :total-price="orderLine.totalPrice"
          />
        </div>
        <span class="flex justify-end font-semibold">Total Price: {{ totalPrice }}</span>
      </div>
    </template>
    <template #modalFooter>
      <button
          type="button"
          @click="closeModal"
          class="ml-2 px-4 py-2 bg-gray-300 rounded-md float-right"
      >
        Close
      </button>
    </template>
  </DefaultModal>
</template>

<style scoped>

</style>