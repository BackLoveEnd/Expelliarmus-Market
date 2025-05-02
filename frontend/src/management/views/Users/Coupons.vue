<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import GlobalCouponsTable from "@/management/components/Users/Coupons/GlobalCouponsTable.vue";
import PersonalCouponsTable from "@/management/components/Users/Coupons/PersonalCouponsTable.vue";
</script>

<template>
  <default-container>
    <div class="space-y-16 my-14">
      <section class="container mx-auto flex flex-col">
        <div>
          <h1 class="font-semibold text-4xl">Coupons</h1>
        </div>
      </section>
      <section class="container mx-auto my-14 flex flex-col gap-y-10">
        <div>
          <h1 class="font-semibold text-xl">Global Coupons</h1>
        </div>
        <div>
          <Suspense>
            <global-coupons-table/>
            <template #fallback>
              <suspense-loader/>
            </template>
          </Suspense>
        </div>
      </section>
      <section class="container mx-auto my-14 flex flex-col gap-y-10">
        <div>
          <h1 class="font-semibold text-xl">Personal Coupons</h1>
        </div>
        <div>
          <Suspense>
            <personal-coupons-table/>
            <template #fallback>
              <suspense-loader/>
            </template>
          </Suspense>
        </div>
      </section>
    </div>
  </default-container>
</template>

<style scoped>

</style>