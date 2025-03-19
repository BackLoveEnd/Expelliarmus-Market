<template>
  <section class="w-full">
    <span class="text-xl font-medium text-[#db4444]">My Orders</span>
  </section>
  <section class="mx-auto w-full max-w-md rounded-2xl bg-white p-2 space-y-6">
    <Disclosure v-slot="{ open }">
      <DisclosureButton
          class="flex w-full justify-between rounded-lg bg-white shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] px-4 py-2 text-left text-sm font-medium hover:bg-gray-100 focus:outline-none focus-visible:ring focus-visible:ring-[#e8a439]"
      >
        <div class="flex flex-col w-full gap-4">
          <div class="flex justify-between items-center">
            <div class="flex flex-col">
              <span class="text-sm font-bold">№ 501467<span class="ml-2 text-xs text-gray-700">from 2 January 2004</span></span>
              <span class="text-green-700 text-xs">Completed</span>
            </div>
            <div>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
              </svg>
            </div>
          </div>
          <div class="flex justify-between items-center">
            <div class="w-14 h-14">
              <img src="https://dummyimage.com/54x54/000/fff" alt="">
            </div>
            <div class="flex flex-col text-center">
              <span class="text-gray-700 text-xs">Price</span>
              <span class="font-bold text-xs">$300</span>
            </div>
          </div>
          <DisclosurePanel v-if="open" class="space-y-4 last-of-type:mb-4">
            <hr class="h-px bg-gray-300 border-0">
            <div class="flex flex-col space-y-2">
              <span class="font-normal">Game Alal alall allalda lalala 9adad (adlad)adadadadadada</span>
              <div class="flex justify-between">
                <span class="font-normal">$199 x per unit</span>
                <span class="font-bold">$199</span>
              </div>
            </div>
            <hr class="h-px bg-gray-300 border-0">
            <div class="flex flex-col space-y-2">
              <div class="flex justify-between">
                <span>Payment Option</span>
                <span>Card</span>
              </div>
              <div class="flex justify-between">
                <span>Discount</span>
                <span>0%</span>
              </div>
              <div class="flex justify-between items-center">
                <span>Total</span>
                <span class="font-semibold text-lg">$199</span>
              </div>
            </div>
            <hr class="h-px bg-gray-300 border-0">
            <div class="flex flex-col">
              <p>Recipient of the order</p>
              <span>Ihor</span>
              <span>+00000000000</span>
              <span>example@gmail.com</span>
            </div>
            <div class="flex justify-center items-center">
              <button type="button"
                      class="px-8 py-3 bg-blue-800 text-white text-center hover:bg-blue-500 rounded-md">
                Repeat order
              </button>
            </div>
          </DisclosurePanel>
        </div>
      </DisclosureButton>
    </Disclosure>
  </section>
</template>

<script>

import {defineComponent} from "vue";
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";

export default defineComponent({
  components: {DisclosurePanel, DisclosureButton, Disclosure}
})
</script>

<style scoped>

</style>