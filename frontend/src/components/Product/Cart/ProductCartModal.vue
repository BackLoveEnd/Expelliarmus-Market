<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" @close="emitCloseModal" class="relative z-10">
      <TransitionChild
        as="template"
        enter="duration-300 ease-out"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="duration-200 ease-in"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/25" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div
          class="flex min-h-full items-center justify-center p-4 text-center"
        >
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <DialogPanel
              class="w-full max-w-3xl transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all"
            >
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-gray-900"
              >
                <div class="flex justify-between items-center">
                  <span>Your Cart</span>
                  <button @click="emitCloseModal">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="size-6"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18 18 6M6 6l12 12"
                      />
                    </svg>
                  </button>
                </div>
              </DialogTitle>
              <div v-if="products.length > 0">
                <div class="overflow-y-auto max-h-72">
                  <div
                    class="mt-10 space-y-8"
                    v-for="product in products"
                    :key="product.id"
                  >
                    <div
                      class="flex justify-between items-center gap-x-8 relative px-6"
                    >
                      <div class="pl-4">
                        <product-checkout-card
                          :price="'$' + product.subTotal"
                          :product-name="product.name"
                          :image="product.image"
                        ></product-checkout-card>
                      </div>
                      <quantity-adjuster
                        v-model="product.quantity"
                      ></quantity-adjuster>
                      <button
                        class="absolute top-3.5 left-0 text-red-600"
                        @click="removeProduct(product.id)"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                          class="size-6"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="flex justify-between items-center my-5">
                  <span class="text-xl font-medium">Total:</span>
                  <span class="font-medium text-xl">${{ totalPrice }}</span>
                </div>

                <div class="mt-5">
                  <button
                    type="button"
                    class="float-end px-8 py-2 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
                    aria-label="Proceed to Checkout"
                  >
                    Proceed to Checkout
                  </button>
                </div>
              </div>

              <div v-else class="flex justify-center items-center py-10">
                <div>
                  <span class="font-semibold text-2xl">Cart Is Empty :(</span>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script setup>
import { computed, defineProps, inject, reactive, ref, watch } from "vue";
import {
  Dialog,
  DialogPanel,
  DialogTitle,
  TransitionChild,
  TransitionRoot,
} from "@headlessui/vue";
import ProductCheckoutCard from "@/shop/views/Order/ProductCheckoutCard.vue";
import QuantityAdjuster from "@/components/Product/Main/QuantityAdjuster.vue";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

const emitter = inject("emitter");

const emit = defineEmits(["close-cart-modal"]);

const quantity = ref(1);

const products = reactive([
  {
    id: 12321,
    name: "LCD Monitor",
    image: "https://dummyimage.com/54x54/000/fff",
    productPrice: 100,
    quantity: 1,
    subTotal: 100,
  },
  {
    id: 12331,
    name: "LCD Monitor",
    image: "https://dummyimage.com/54x54/000/fff",
    productPrice: 100,
    quantity: 1,
    subTotal: 100,
  },
  {
    id: 12341,
    name: "LCD Monitor",
    image: "https://dummyimage.com/54x54/000/fff",
    productPrice: 100,
    quantity: 1,
    subTotal: 100,
  },
  {
    id: 12335,
    name: "LCD Monitor",
    image: "https://dummyimage.com/54x54/000/fff",
    productPrice: 100,
    quantity: 1,
    subTotal: 100,
  },
  {
    id: 12135,
    name: "LCD Monitor",
    image: "https://dummyimage.com/54x54/000/fff",
    productPrice: 100,
    quantity: 1,
    subTotal: 100,
  },
]);

function emitCloseModal() {
  emit("close-cart-modal");
}

function removeProduct(id) {
  const index = products.findIndex((item) => item.id === id);
  if (index !== -1) {
    products.splice(index, 1);
    emitter.emit("remove-from-cart");
  }
}

const totalPrice = computed(() => {
  return products.reduce((total, product) => total + product.subTotal, 0);
});

watch(
  () => products,
  (newProducts) => {
    newProducts.forEach(
      (product) => (product.subTotal = product.quantity * product.productPrice),
    );
  },
  { deep: true },
);
</script>

<style scoped></style>
