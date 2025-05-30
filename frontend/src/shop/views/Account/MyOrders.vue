<template>
  <section class="w-full">
    <span class="text-xl font-medium text-[#db4444]" id="ordersAnchor">My Orders</span>
  </section>
  <section class="mx-auto w-full max-w-md rounded-2xl bg-white p-2 space-y-6">
    <suspense-loader v-if="isLoading"/>
    <template v-if="isLoading === false && orders.length > 0">
      <div v-for="order in orders" :key="order.orderId">
        <Disclosure v-slot="{ open }">
          <DisclosureButton
              class="flex w-full justify-between rounded-lg bg-white shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] px-4 py-2 text-left text-sm font-medium hover:bg-gray-100 focus:outline-none focus-visible:ring focus-visible:ring-[#e8a439]"
          >
            <div class="flex flex-col w-full gap-4">
              <div class="flex justify-between items-center">
                <div class="flex flex-col">
              <span class="text-sm font-bold">№ {{ order.orderId }}<span
                  class="ml-2 text-xs text-gray-700">from {{ new Date(order.createdAt).toLocaleString() }}</span></span>
                  <span class="text-xs" :class="getStatusColor(order.status)">{{
                      upperCaseFirstLetter(order.status)
                    }}</span>
                </div>
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                       stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                  </svg>
                </div>
              </div>
              <div class="flex justify-between items-center">
                <div class="w-14 h-14" v-if="order.positions.length === 1">
                  <img :src="order.positions[0].productImage" alt="Product" class="object-cover rounded-md"/>
                </div>
                <div v-else>
                  <span class="text-xs">Products in Order: {{ order.positions.length }}</span>
                </div>
                <div class="flex flex-col text-center">
                  <span class="text-gray-700 text-xs">Price</span>
                  <span class="font-bold text-xs">${{ order.total }}</span>
                </div>
              </div>
              <DisclosurePanel v-if="open" class="space-y-4 last-of-type:mb-4">
                <hr class="h-px bg-gray-300 border-0">
                <div v-if="order.positions.length === 1" class="flex flex-col space-y-2">
                  <span class="font-normal">{{ order.positions[0].productTitle }}</span>
                  <div class="flex justify-between">
                    <span class="font-normal">Per Unit: ${{ order.positions[0].pricePerUnit }}</span>
                    <span class="font-normal">Quantity: {{ order.positions[0].quantity }}x</span>
                    <span class="font-bold">Subtotal: ${{ order.positions[0].totalPrice }}</span>
                  </div>
                </div>
                <div v-else class="flex flex-col space-y-4">
                  <div
                      v-for="(position, idx) in order.positions"
                      :key="idx"
                      class="flex items-center gap-4 border p-2 rounded-lg"
                  >
                    <img :src="position.productImage" alt="Product" class="w-14 h-14 object-cover rounded-md"/>
                    <div class="flex flex-col">
                      <span class="font-normal">{{ truncator.truncateString(position.productTitle, 40) }}</span>
                      <div class="flex flex-col text-xs">
                        <span class="font-normal">Per Unit: ${{ position.pricePerUnit }}</span>
                        <span class="font-normal">Quantity: {{ position.quantity }}x</span>
                        <span class="font-bold">Subtotal: ${{ position.totalPrice }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                <hr class="h-px bg-gray-300 border-0">
                <div class="flex flex-col space-y-2">
                  <div class="flex justify-between">
                    <span>Payment Option</span>
                    <span>Card</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span>Total</span>
                    <span class="font-semibold text-lg">${{ order.total }}</span>
                  </div>
                </div>
                <hr class="h-px bg-gray-300 border-0">
                <div class="flex flex-col">
                  <p>Recipient of the order</p>
                  <span>{{ auth.fullName }}</span>
                  <span>{{ auth.phone_original }}</span>
                  <span>{{ auth.email }}</span>
                </div>
                <div class="flex justify-center" v-if="canCancelOrder(order.status)">
                  <button
                      type="button"
                      @click.stop="openCancelOrderModal(order.orderId)"
                      class="py-2 px-4 bg-red-500 text-white rounded-md z-50">
                    Cancel Order
                  </button>
                </div>
              </DisclosurePanel>
            </div>
          </DisclosureButton>
        </Disclosure>
      </div>
    </template>
    <div v-else-if="isLoading === false && orders.length === 0">
      <main class="grid place-items-center">
        <div class="text-center">
          <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">You haven't had any orders yet.</p>
          <div class="mt-10 flex items-center justify-center gap-x-6">
            <router-link
                :to="{ name: 'home' }"
                class="rounded-md bg-yellow-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-yellow-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-600">
              Make your first order
            </router-link>
            <a href="#" class="text-sm font-semibold text-gray-900">Contact support <span
                aria-hidden="true">&rarr;</span></a>
          </div>
        </div>
      </main>
    </div>
    <section class="flex justify-between items-center" v-if="orders.length > 0">
      <span class="text-xs">Showing page {{ metaData.currentPage }} of {{ metaData.lastPage }}.</span>
      <div class="flex justify-end">
        <button
            type="button"
            @click.prevent="scroller.scrollToView('panelContainer')"
            @click="getOrders(links.prev)"
            :disabled="links.prev === null"
            class="flex items-center justify-center px-3 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
          Previous
        </button>

        <button
            type="button"
            @click.prevent="scroller.scrollToView('panelContainer')"
            @click="getOrders(links.next)"
            :disabled="links.next === null"
            class="flex items-center justify-center px-3 h-8 ms-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
          Next
        </button>
      </div>
    </section>
  </section>
  <cancel-order-modal
      :is-open="isCancelOrderModalOpen"
      :order-id="orderId"
      @close-modal="isCancelOrderModalOpen = false"
      @order-cancelled="handleOrderCancel"
      @failed="toast.showToast('Failed to cancel order. Please, contact us.', defaultErrorSettings)"
  />
</template>

<script setup>

import {Disclosure, DisclosureButton, DisclosurePanel} from '@headlessui/vue';
import {ShopOrderService} from '@/services/Order/ShopOrderService.js';
import {onMounted, reactive, ref} from 'vue';
import SuspenseLoader from '@/components/Default/SuspenseLoader.vue';
import {useTruncator} from '@/composables/useTruncator.js';
import {useAuthStore} from '@/stores/useAuthStore.js';
import {useScrolling} from '@/composables/useScrolling.js';
import CancelOrderModal from "@/shop/components/Order/CancelOrderModal.vue";
import {useToastStore} from "@/stores/useToastStore.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";

const truncator = useTruncator();

const scroller = useScrolling();

const auth = useAuthStore();

const orders = ref([]);

const metaData = reactive({
  currentPage: null,
  lastPage: null,
  total: null,
  perPage: null,
});

const links = reactive({
  first: null,
  last: null,
  prev: null,
  next: null,
});

const orderId = ref(null);

const toast = useToastStore();

const isLoading = ref(false);

const isCancelOrderModalOpen = ref(false);

function openCancelOrderModal(id) {
  orderId.value = id;

  isCancelOrderModalOpen.value = true;
}

async function getOrders(page = 1) {
  isLoading.value = true;

  await ShopOrderService.getMyOrders(page)
      .then((response) => {
        orders.value = response.data;

        Object.assign(metaData, response.meta);

        Object.assign(links, response.links);
      })
      .catch((e) => {

      })
      .finally(() => {
        isLoading.value = false;
      });
}

const handleOrderCancel = async () => {
  toast.showToast('Order was cancelled.', defaultSuccessSettings);

  await getOrders(1);
};

function getStatusColor(status) {
  switch (status) {
    case 'pending':
      return 'text-yellow-500';
    case 'delivered':
      return 'text-green-500';
    case 'canceled':
      return 'text-red-500';
    case 'refunded':
      return 'text-blue-500';
    case 'in progress':
      return 'text-gray-500';
    default:
      return '';
  }
}

function upperCaseFirstLetter(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function canCancelOrder(status) {
  switch (status) {
    case 'pending':
      return true;
    case 'delivered':
      return false;
    case 'canceled':
      return false;
    case 'refunded':
      return false;
    case 'in progress':
      return true;
    default:
      return false;
  }
}

onMounted(() => getOrders());

</script>

<style scoped>

</style>