<script setup>
import BaseTextInput from "@/components/Default/Inputs/BaseTextInput.vue";
import {ref} from "vue";
import {ShopCouponService} from "@/services/User/ShopCouponService.js";

const couponCode = ref(null);

const errorMessage = ref(null);

const emit = defineEmits(["coupon-applied"]);

async function checkCoupon() {
  await ShopCouponService.checkForCouponCode(couponCode.value)
      .then((response) => {
        errorMessage.value = null;

        emit("coupon-applied", response?.data?.data?.attributes);
      })
      .catch((e) => {
        if (e?.status === 422 || e?.status === 403) {
          errorMessage.value = e?.response?.data?.message;
        }
      });
}
</script>

<template>
  <div class="flex flex-col gap-y-2">
    <div class="flex gap-x-4 items-center">
      <base-text-input
          id="coupon"
          name="coupon"
          v-model="couponCode"
          placeholder="3AB2C3D4"
      ></base-text-input>
      <button
          type="button"
          @click="checkCoupon"
          class="px-12 py-1 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
      >
        Apply Coupon
      </button>
    </div>
    <span class="text-xs text-red-400">{{ errorMessage }}</span>
  </div>
</template>

<style scoped>

</style>