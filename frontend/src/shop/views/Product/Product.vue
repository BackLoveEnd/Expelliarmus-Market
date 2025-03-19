<template>
  <main class="space-y-32 last-of-type:mb-20">
    <div class="space-y-20 mt-20">
      <section class="container mx-auto max-w-screen-2xl">
        <bread-crumbs :links="links"></bread-crumbs>
      </section>
      <!--      <section class="container mx-auto">
              <div class="flex justify-between">
                <product-photo-tabs></product-photo-tabs>
                <div class="flex flex-col justify-between items-start">
                  <div class="flex flex-col gap-y-4">
                    <description
                      :price="priceDependOnQuantity"
                      :price-per-unit="pricePerUnit"
                    ></description>
                  </div>
                  <div class="flex flex-col gap-y-8">
                    <colors :colors="colors"></colors>
                    <sizes :sizes="sizes"></sizes>
                  </div>
                  <div class="flex items-center gap-x-8">
                    <quantity-adjuster v-model="quantity"></quantity-adjuster>
                    <div>
                      <purchase-button
                        @open-cart-modal="toggleCartModal"
                      ></purchase-button>
                      <product-cart-modal
                        :is-open="isCartModalOpen"
                        @close-cart-modal="toggleCartModal"
                      ></product-cart-modal>
                    </div>
                    <div>
                      <button
                        @click="addToWishList"
                        :class="{ active: isInWishlist }"
                        class="wishlist px-3 py-2 text-white text-center hover:bg-gray-200 border border-gray-700 rounded-md"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke-width="1.5"
                          stroke="currentColor"
                          class="size-6 text-black"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
                          />
                        </svg>
                      </button>
                    </div>
                  </div>
                  <div class="border border-gray-700 rounded-md w-full">
                    <div
                      class="flex justify-start items-center border-b border-gray-700 py-8 px-8"
                    >
                      <div class="flex gap-x-6 items-center">
                        <svg
                          viewBox="0 0 24 24"
                          fill="black"
                          xmlns="http://www.w3.org/2000/svg"
                          width="40"
                          height="40"
                          color="black"
                        >
                          <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M4.25 3.25C3.00736 3.25 2 4.25736 2 5.5V16C2 17.2426 3.00736 18.25 4.25 18.25H4.30197C4.56712 19.6729 5.81527 20.75 7.315 20.75C8.81473 20.75 10.0629 19.6729 10.328 18.25H14.052C14.3171 19.6729 15.5653 20.75 17.065 20.75C18.5647 20.75 19.8129 19.6729 20.078 18.25H22C22.4142 18.25 22.75 17.9142 22.75 17.5C22.75 17.0858 22.4142 16.75 22 16.75V12.4047C22 11.9553 21.8655 11.5163 21.6137 11.1441L19.0674 7.37945C18.6489 6.76072 17.9506 6.39003 17.2037 6.39003H15.75V5.5C15.75 4.25736 14.7426 3.25 13.5 3.25H4.25ZM7.315 14.62C5.94831 14.62 4.79055 15.5145 4.39523 16.75H4.25C3.83579 16.75 3.5 16.4142 3.5 16V5.5C3.5 5.08579 3.83579 4.75 4.25 4.75H13.5C13.9142 4.75 14.25 5.08579 14.25 5.5V16.4706C14.2107 16.5615 14.1757 16.6547 14.1452 16.75H10.2348C9.83945 15.5145 8.68169 14.62 7.315 14.62ZM17.065 14.62C16.5944 14.62 16.1485 14.7261 15.75 14.9156V12.695L20.5 12.695V16.75H19.9848C19.5895 15.5145 18.4317 14.62 17.065 14.62ZM19.8373 11.195L15.75 11.195V7.89003H17.2037C17.4527 7.89003 17.6854 8.01359 17.8249 8.21983L19.8373 11.195ZM15.5 17.685C15.5 16.8207 16.2007 16.12 17.065 16.12C17.9293 16.12 18.63 16.8207 18.63 17.685C18.63 18.5493 17.9293 19.25 17.065 19.25C16.2007 19.25 15.5 18.5493 15.5 17.685ZM5.75 17.685C5.75 16.8207 6.45067 16.12 7.315 16.12C8.17933 16.12 8.88 16.8207 8.88 17.685C8.88 18.5493 8.17933 19.25 7.315 19.25C6.45067 19.25 5.75 18.5493 5.75 17.685Z"
                            fill="black"
                          ></path>
                        </svg>
                        <div class="flex flex-col">
                          <span class="font-medium text-base">Free Delivery</span>
                        </div>
                      </div>
                    </div>
                    <div class="flex justify-start items-center py-8 px-8">
                      <div class="flex gap-x-6 items-center">
                        <svg
                          width="40"
                          height="40"
                          viewBox="0 0 25 24"
                          fill="black"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                            d="M3.13644 9.54175C3.02923 9.94185 3.26667 10.3531 3.66676 10.4603C4.06687 10.5675 4.47812 10.3301 4.58533 9.92998C5.04109 8.22904 6.04538 6.72602 7.44243 5.65403C8.83948 4.58203 10.5512 4.00098 12.3122 4.00098C14.0731 4.00098 15.7848 4.58203 17.1819 5.65403C18.3999 6.58866 19.3194 7.85095 19.8371 9.28639L18.162 8.34314C17.801 8.1399 17.3437 8.26774 17.1405 8.62867C16.9372 8.98959 17.0651 9.44694 17.426 9.65017L20.5067 11.3849C20.68 11.4825 20.885 11.5072 21.0766 11.4537C21.2682 11.4001 21.4306 11.2727 21.5282 11.0993L23.2629 8.01828C23.4661 7.65734 23.3382 7.2 22.9773 6.99679C22.6163 6.79358 22.159 6.92145 21.9558 7.28239L21.195 8.63372C20.5715 6.98861 19.5007 5.54258 18.095 4.464C16.436 3.19099 14.4033 2.50098 12.3122 2.50098C10.221 2.50098 8.1883 3.19099 6.52928 4.464C4.87027 5.737 3.67766 7.52186 3.13644 9.54175Z"
                            fill="black"
                          />
                          <path
                            d="M21.4906 14.4582C21.5978 14.0581 21.3604 13.6469 20.9603 13.5397C20.5602 13.4325 20.1489 13.6699 20.0417 14.07C19.5859 15.7709 18.5816 17.274 17.1846 18.346C15.7875 19.418 14.0758 19.999 12.3149 19.999C10.5539 19.999 8.84219 19.418 7.44514 18.346C6.2292 17.4129 5.31079 16.1534 4.79261 14.721L6.45529 15.6573C6.81622 15.8605 7.27356 15.7327 7.47679 15.3718C7.68003 15.0108 7.55219 14.5535 7.19127 14.3502L4.11056 12.6155C3.93723 12.5179 3.73222 12.4932 3.54065 12.5467C3.34907 12.6003 3.18662 12.7278 3.08903 12.9011L1.3544 15.9821C1.15119 16.3431 1.27906 16.8004 1.64 17.0036C2.00094 17.2068 2.45828 17.079 2.66149 16.718L3.42822 15.3562C4.05115 17.0054 5.12348 18.4552 6.532 19.536C8.19102 20.809 10.2237 21.499 12.3149 21.499C14.406 21.499 16.4387 20.809 18.0977 19.536C19.7568 18.263 20.9494 16.4781 21.4906 14.4582Z"
                            fill="black"
                          />
                        </svg>
                        <div class="flex flex-col">
                          <span class="font-medium text-base">Return Delivery</span>
                          <span class="text-sm">Free 30 Days Delivery Returns</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>-->
    </div>
    <section class="container mx-auto space-y-8 max-w-screen-2xl">
      <section-title title="Specs"></section-title>
      <article class="w-1/2">
        <specs></specs>
      </article>
    </section>
    <section class="container mx-auto mt-32 max-w-screen-2xl">
      <section-title title="Related Item"></section-title>
      <div class="grid grid-cols-5 gap-11">
        <product-card />
        <product-card />
        <product-card />
        <product-card />
        <product-card />
      </div>
    </section>
  </main>
</template>

<script setup>
import BreadCrumbs from "@/components/Default/BreadCrumbs.vue";
import SectionTitle from "@/components/Default/SectionTitle.vue";
import ProductCard from "@/components/Card/ProductCard.vue";
import Specs from "@/components/Product/Main/Specs.vue";
import { computed, inject, ref } from "vue";

const emitter = inject("emitter");

const links = ref([
  { url: "/", name: "Home" },
  { url: "/", name: "Category" },
  { url: "/", name: "Product Name" },
]);

const colors = ref([
  { value: "red", color: "#e07575" },
  { value: "blue", color: "#0c8ce9" },
]);

const sizes = ref([
  { value: "xs", name: "XS" },
  { value: "s", name: "S" },
  { value: "m", name: "M" },
  { value: "l", name: "L" },
  { value: "xl", name: "XL" },
]);

const quantity = ref(1);
const price = ref(198);
const isInWishlist = ref(false);
const isCartModalOpen = ref(false);

function addToWishList() {
  isInWishlist.value = !isInWishlist.value;

  isInWishlist.value
    ? emitter.emit("add-to-wishlist")
    : emitter.emit("remove-from-wishlist");
}

function toggleCartModal() {
  isCartModalOpen.value = !isCartModalOpen.value;
}

const priceDependOnQuantity = computed(() =>
  (price.value * quantity.value).toFixed(2),
);

const pricePerUnit = computed(() => price.value.toFixed(2));
</script>

<style scoped>
.wishlist svg {
  stroke: currentColor;
}

.wishlist.active svg {
  fill: #db4444;
  stroke: #db4444;
}
</style>
