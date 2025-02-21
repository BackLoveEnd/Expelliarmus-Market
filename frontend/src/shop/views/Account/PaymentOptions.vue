<template>
  <section class="w-full">
    <span class="text-xl font-medium text-[#db4444]">Payment Option</span>
  </section>
  <section class="text-black">
    <form action="#" class="w-full p-4">
      <account-credit-card/>
      <section class="flex justify-end gap-x-8">
        <button type="button" class="text-black text-center">
          Cancel
        </button>
        <button type="button"
                class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md">
          Save Changes
        </button>
      </section>
    </form>
  </section>
</template>

<script>

import {TabPanel} from "@headlessui/vue";
import AccountCreditCard from "@/shop/views/Account/AccountCreditCard.vue";

export default {
  components: {AccountCreditCard, TabPanel},
}
</script>

<style scoped>

</style>