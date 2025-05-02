<template>
  <section class="w-full">
    <span class="text-xl font-medium text-[#db4444]" id="ordersAnchor">My Coupons</span>
  </section>
  <section class="mx-auto w-full space-y-6">
    <div class="flex flex-col gap-y-8">
      <div class="flex flex-col gap-y-6">
        <span class="ml-10">Personal Coupons</span>
        <suspense>
          <personal-user-coupons/>
          <template #fallback>
            <suspense-loader/>
          </template>
        </suspense>
      </div>
      <div class="flex flex-col gap-y-6">
        <span class="ml-10">Global Coupons</span>
        <suspense>
          <global-user-coupons/>
          <template #fallback>
            <suspense-loader/>
          </template>
        </suspense>
      </div>
    </div>
  </section>
</template>

<script setup>

import PersonalUserCoupons from '@/shop/components/Coupons/PersonalUserCoupons.vue'
import SuspenseLoader from '@/components/Default/SuspenseLoader.vue'
import GlobalUserCoupons from '@/shop/components/Coupons/GlobalUserCoupons.vue'
</script>

<style scoped>

</style>