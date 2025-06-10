<template>
  <main class="space-y-20 first-of-type:mt-20 last-of-type:mb-20" id="panelContainer">
    <section class="container mx-auto">
      <div class="flex justify-between">
        <bread-crumbs :links="links"></bread-crumbs>
        <span class="text-sm"
        >Welcome!
          <span class="text-[#db4444]">{{ auth.fullName }}</span></span
        >
      </div>
    </section>
    <section class="container mx-auto grid grid-cols-7 grid-rows-5 gap-8">
      <tab-group :selectedIndex="selectedTab" @change="changeTab">
        <aside class="col-span-2 row-span-5 space-y-6">
          <div class="flex flex-col gap-3">
            <span class="text-lg font-medium">Manage My Account</span>
            <tab-list class="flex flex-col gap-y-2 items-start ps-8 text-base">
              <tab as="template" v-slot="{ selected }">
                <button
                    :class="{
                    'text-[#db4444]': selected,
                    'text-[#808080]': !selected,
                  }"
                    class="focus:outline-none"
                >
                  My Profile
                </button>
              </tab>
              <!--              <tab as="template" v-slot="{ selected }">
                              <button
                                  :class="{
                                                'text-[#db4444]': selected,
                                                'text-[#808080]': !selected,
                                              }"
                                  class="focus:outline-none"
                              >
                                Access Book
                              </button>
                            </tab>
                            <tab as="template" v-slot="{ selected }">
                              <button
                                  :class="{
                                                'text-[#db4444]': selected,
                                                'text-[#808080]': !selected,
                                              }"
                                  class="focus:outline-none"
                              >
                                My Payment Options
                              </button>
                            </tab>-->
            </tab-list>
          </div>
          <div class="flex flex-col gap-3">
            <span class="text-lg font-medium">My Orders</span>
            <tab-list class="flex flex-col gap-y-2 ps-8 items-start text-base">
              <tab as="template" v-slot="{ selected }">
                <button
                    :class="{
                    'text-[#db4444]': selected,
                    'text-[#808080]': !selected,
                  }"
                    class="focus:outline-none"
                >
                  My Orders
                </button>
              </tab>
              <tab as="template" v-slot="{ selected }">
                <button
                    :class="{
                    'text-[#db4444]': selected,
                    'text-[#808080]': !selected,
                  }"
                    class="focus:outline-none"
                >
                  My Cancellations
                </button>
              </tab>
            </tab-list>
          </div>
          <div class="flex flex-col gap-3">
            <span class="text-lg font-medium">My Coupons</span>
            <tab-list class="flex flex-col gap-y-2 ps-8 items-start text-base">
              <tab as="template" v-slot="{ selected }">
                <button
                    :class="{
                    'text-[#db4444]': selected,
                    'text-[#808080]': !selected,
                  }"
                    class="focus:outline-none"
                >
                  My Coupons
                </button>
              </tab>
            </tab-list>
          </div>
        </aside>
        <tab-panels
            class="col-span-5 row-span-5 col-start-3 bg-white shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] rounded-md"
        >
          <tab-panel v-show="selectedTab === 0" class="py-10 px-20 space-y-8">
            <profile-info/>
          </tab-panel>
          <!--          <tab-panel v-show="selectedTab === 1" class="py-10 px-20 space-y-8">

                    </tab-panel>-->
          <!--          <tab-panel v-show="selectedTab === 2" class="py-10 px-20 space-y-8">
                      <payment-options/>
                    </tab-panel>-->
          <tab-panel v-show="selectedTab === 1" class="py-10 px-20 space-y-8">
            <my-orders/>
          </tab-panel>
          <tab-panel v-show="selectedTab === 2" class="py-10 px-20 space-y-8">
            <my-cancellations/>
          </tab-panel>
          <tab-panel v-show="selectedTab === 3" class="py-10 px-20 space-y-8">
            <my-coupons/>
          </tab-panel>
        </tab-panels>
      </tab-group>
    </section>
  </main>
</template>

<script setup>
import {defineAsyncComponent, ref} from 'vue';
import BreadCrumbs from '@/components/Default/BreadCrumbs.vue';
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from '@headlessui/vue';
import {useAuthStore} from '@/stores/useAuthStore.js';
import {useRoute} from 'vue-router';

const ProfileInfo = defineAsyncComponent(
    () => import('@/shop/views/Account/ProfileInfo.vue'),
);
const PaymentOptions = defineAsyncComponent(
    () => import('@/shop/views/Account/PaymentOptions.vue'),
);
const MyOrders = defineAsyncComponent(
    () => import('@/shop/views/Account/MyOrders.vue'),
);
const MyCancellations = defineAsyncComponent(
    () => import('@/shop/views/Account/MyCancellations.vue'),
);
const MyCoupons = defineAsyncComponent(
    () => import('@/shop/views/Account/MyCoupons.vue'),
);

const auth = useAuthStore();

const links = ref([
  {url: '/', name: 'Home'},
  {url: '/account?tab=0', name: 'Account'},
]);

const route = useRoute();

const selectedTab = ref(Number(route.query.tab) ?? 0);

function changeTab(index) {
  selectedTab.value = index;
}
</script>

<style scoped></style>
