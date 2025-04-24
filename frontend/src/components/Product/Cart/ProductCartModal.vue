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
        <div class="fixed inset-0 bg-black/25"/>
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
              <div v-if="productLength > 0">
                <div class="overflow-y-auto max-h-72">
                  <div
                      class="mt-10 space-y-8"
                      v-for="product in cartStore.cartItems"
                      :key="product.productId"
                  >
                    <div
                        class="flex justify-between items-center gap-x-8 relative px-6"
                    >
                      <div class="pl-4">
                        <product-checkout-card
                            :price="product.unitPrice"
                            :product-name="truncator.truncateString(product.productTitle, 20).value"
                            :image="product.productImage"
                            :product-link="{
                              id: product.productId,
                              slug: product.productSlug
                            }"
                            :discount="product.discount"
                        />
                      </div>
                      <quantity-adjuster
                          v-model="product.quantity"
                      />
                      <button
                          class="absolute top-3.5 left-0 text-red-600"
                          @click="cartStore.removeFromCart(product.id)"
                      >
                        <i class="pi pi-times-circle text-red-500"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <div class="flex justify-between items-center my-5">
                  <span class="text-xl font-medium">Total:</span>
                  <span class="font-medium text-xl">${{ totalPrice }}</span>
                </div>

                <div class="mt-5">
                  <router-link
                      to="/checkout"
                      @click.prevent="useScrolling().scrollToTop()"
                      class="float-end px-8 py-2 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
                      aria-label="Proceed to Checkout"
                  >
                    Proceed to Checkout
                  </router-link>
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
import {computed, defineProps, ref, watch} from "vue";
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot,} from "@headlessui/vue";
import ProductCheckoutCard from "@/shop/views/Order/ProductCheckoutCard.vue";
import QuantityAdjuster from "@/components/Product/Main/QuantityAdjuster.vue";
import {useCartStore} from "@/stores/useCartStore.js";
import {useTruncator} from "@/composables/useTruncator.js";
import {useScrolling} from "@/composables/useScrolling.js";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close-cart-modal"]);

const truncator = useTruncator();

const cartStore = useCartStore();

const productLength = ref(0);

function emitCloseModal() {
  emit("close-cart-modal");
}

const totalPrice = computed(() => {
  return cartStore.cartItems.reduce((total, product) => {
    const price = product.discount ? product.discount.new_price : product.unitPrice;
    return total + price * product.quantity;
  }, 0).toFixed(2);
});

async function getCart() {
  await cartStore.fetchCart()
      .then(() => {

      })
      .catch(() => {

      });
}

watch(() => props.isOpen, async (newVal) => {
  if (newVal) {
    await getCart();

    productLength.value = cartStore.totalItems;
  }
});
</script>

<style scoped></style>
