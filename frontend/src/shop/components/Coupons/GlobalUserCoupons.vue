<script setup>
import CouponCard from "@/shop/components/Coupons/CouponCard.vue";
import {ShopCouponService} from "@/services/User/ShopCouponService.js";
import {ref} from "vue";

const coupons = ref([]);

const nextPageNumber = ref(null);

const previousPageNumber = ref(null);

const total = ref(null);

async function getCoupons(page) {
  await ShopCouponService.getMyGlobalCoupon(page)
      .then((response) => {
        coupons.value = response?.data?.data?.map((coupon) => ({
          couponCode: coupon.attributes.coupon,
          expiresAt: new Date(coupon.attributes.expires_at).toLocaleDateString("en-US", {
            month: "long",
            day: "numeric",
            year: "numeric"
          }),
          discount: coupon.attributes.discount,
          usageNumber: coupon.attributes.usage_number
        }));

        nextPageNumber.value = response?.data?.links?.next;

        total.value = response?.data?.meta?.total;

        previousPageNumber.value = response?.data?.links?.prev;
      })
      .catch((e) => {

      });
}

await getCoupons(1);
</script>

<template>
  <div class="flex justify-center items-center flex-col gap-y-6" v-if="coupons.length">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 w-full max-w-4xl px-4">
      <coupon-card
          class="border-blue-400"
          v-for="coupon in coupons"
          :key="coupon.couponCode"
          title="Public Coupon"
          :coupon-code="coupon.couponCode"
          :discount="coupon.discount"
          :expires-at-date="coupon.expiresAt"
          :usage-number="coupon.usageNumber"
          type="global"
      />
    </div>

    <div class="flex" v-if="coupons.length < total">
      <button
          type="button"
          @click="getCoupons(previousPageNumber)"
          :disabled="previousPageNumber === null"
          class="flex items-center justify-center px-3 h-8 me-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
        <svg class="w-3.5 h-3.5 me-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 14 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 5H1m0 0 4 4M1 5l4-4"/>
        </svg>
        Previous
      </button>
      <button
          @click="getCoupons(nextPageNumber)"
          type="button"
          :disabled="nextPageNumber === null"
          class="flex items-center justify-center px-3 h-8 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
        Next
        <svg class="w-3.5 h-3.5 ms-2 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
             viewBox="0 0 14 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M1 5h12m0 0L9 1m4 4L9 9"/>
        </svg>
      </button>
    </div>
  </div>
  <div v-else>
    <main class="grid place-items-center">
      <div class="text-center">
        <p class="mt-6 text-lg font-medium text-pretty text-gray-500 sm:text-xl/8">Global coupons not found.</p>
      </div>
    </main>
  </div>
</template>

<style scoped>

</style>