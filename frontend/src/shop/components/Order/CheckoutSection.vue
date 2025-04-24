<script setup>
import {Disclosure, DisclosureButton, DisclosurePanel} from "@headlessui/vue";
import CheckoutCreditCard from "@/shop/views/Order/CheckoutCreditCard.vue";
import ProductsCheckoutOverview from "@/shop/components/Order/ProductsCheckoutOverview.vue";
import RegularUserInfo from "@/shop/components/Order/RegularUserInfo.vue";
import GuestInfo from "@/shop/components/Order/GuestInfo.vue";
import {ref} from "vue";
import {ShopOrderService} from "@/services/Order/ShopOrderService.js";
import {useAuthStore} from "@/stores/useAuthStore.js";
import {useToastStore} from "@/stores/useToastStore.js";
import SuspenseLoader from "@/components/Default/SuspenseLoader.vue";
import {useScrolling} from "@/composables/useScrolling.js";
import OrderCoupon from "@/shop/components/Order/OrderCoupon.vue";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";

const auth = useAuthStore();

const toast = useToastStore();

const userForm = ref();

const emit = defineEmits(['checkout-process-success']);

const isCheckoutProcessLoading = ref(false);

const guestUserData = ref();

const disclosureRef = ref(null);

const coupon = ref();

const handleUserFormData = (data) => {
  if (!data) {
    isCheckoutProcessLoading.value = false;

    return;
  }

  guestUserData.value = data;
};

const handleCheckout = async () => {
  if (!auth.isRegularUser) {
    await userForm?.value?.submit();

    if (!guestUserData.value) {
      toast.showToast("Please fill in your personal information to proceed with checkout.");
      return;
    }
  } else {
    if (!userHasFilledPersonalInfoToProcessCheckout()) {
      toast.showToast("Please fill in your personal information to proceed with checkout.");
      return;
    }
  }

  isCheckoutProcessLoading.value = true;

  await ShopOrderService.createOrder(guestUserData.value ?? null, coupon.value?.coupon ?? null)
      .then((response) => {
        if (response.status === 200) {
          emit('checkout-process-success', response?.data?.data?.order_id);
        }
      })
      .catch((e) => {
        if ([422, 409].includes(e?.status)) {
          toast.showToast(e?.response?.data?.message, defaultErrorSettings);
        }
      })
      .finally(() => isCheckoutProcessLoading.value = false);
};

const handleCouponApplied = (couponData) => {
  coupon.value = couponData;
};

function userHasFilledPersonalInfoToProcessCheckout() {
  return auth?.user?.phone_original && auth?.user?.address;
}

function closeDisclosure() {
  disclosureRef.value.$el.closest('[data-headlessui-state="open"]').click();
}
</script>

<template>
  <section class="container mx-auto space-y-12 relative p-2">
    <h2 class="text-4xl font-medium">Billing Details</h2>
    <div
        v-if="isCheckoutProcessLoading"
        class="absolute inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30 rounded-md"
    >
      <SuspenseLoader class="w-12 h-12"/>
    </div>
    <form>
      <div class="flex justify-between">
        <guest-info v-if="!auth.isRegularUser" ref="userForm" @submit="handleUserFormData"/>
        <div class="w-full max-w-md" v-else>
          <regular-user-info/>
        </div>
        <div class="flex flex-col w-2/5 gap-y-12">
          <products-checkout-overview :coupon="coupon"/>
          <div class="space-y-6">
            <div class="flex flex-col">
              <disclosure v-slot="{ open, close }" ref="disclosureRef">
                <disclosure-button
                    :class="{
                      'flex w-full justify-between rounded-lg bg-white shadow-[0px_1px_9px_0px_rgba(0,_0,_0,_0.1)] px-4 py-2':
                        open,
                      '': !open,
                    }"
                >
                  <div class="flex flex-col w-full gap-4 pb-2">
                    <div class="flex justify-between">
                      <div class="flex items-center gap-x-2">
                        <label
                            class="relative flex items-center cursor-pointer"
                            for="bank"
                        >
                          <input
                              name="payment_type"
                              type="radio"
                              :checked="open"
                              class="peer h-5 w-5 cursor-pointer appearance-none rounded-full border border-slate-300 checked:border-slate-400 transition-all"
                              id="bank"
                          />
                          <span
                              class="absolute bg-slate-800 w-3 h-3 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                          >
                            </span>
                        </label>
                        <span
                            class="ml-2 text-slate-600 cursor-pointer text-sm"
                        >Bank</span
                        >
                      </div>
                      <div class="flex">
                        <svg
                            width="50"
                            height="50"
                            viewBox="0 0 24 24"
                            fill="#343C54"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                              d="M11.0504 12.0142H11.7911C11.8293 11.8004 11.7606 11.6171 11.5239 11.6171C11.2871 11.6171 11.1191 11.7393 11.0504 12.0142Z"
                              fill="#343C54"
                          />
                          <path
                              d="M6.87349 12.4113C6.86251 12.4095 6.85004 12.4073 6.83629 12.4048C6.72557 12.3845 6.53159 12.3491 6.36174 12.4646C6.17078 12.5945 6.11741 12.8233 6.31599 12.9C6.50706 12.9734 6.78916 12.8467 6.82763 12.6477L6.87349 12.4113Z"
                              fill="#343C54"
                          />
                          <path
                              d="M20.4733 12.3424C20.5344 12.144 20.5011 11.8961 20.4351 11.8081C20.252 11.5638 19.8306 11.7569 19.7021 12.2358C19.6181 12.5487 19.8015 12.7854 20.0076 12.7854C20.2291 12.7853 20.3665 12.6097 20.4733 12.3424Z"
                              fill="#343C54"
                          />
                          <path
                              d="M16.9532 12.4113C16.9422 12.4095 16.9297 12.4073 16.916 12.4048C16.8053 12.3845 16.6114 12.3491 16.4416 12.4646C16.2506 12.5945 16.1971 12.8233 16.3958 12.9C16.587 12.9734 16.869 12.8467 16.9072 12.6477L16.9532 12.4113Z"
                              fill="#343C54"
                          />
                          <path
                              fill-rule="evenodd"
                              clip-rule="evenodd"
                              d="M16.0812 6.08019C19.3505 6.08019 22.001 8.73068 22.001 12.0001C22.001 15.2693 19.3505 17.9198 16.0812 17.9197C14.6624 17.9197 13.3715 17.4231 12.352 16.591C13.1297 15.8402 13.7234 14.9 14.0419 13.8374H13.5501C13.2418 14.7823 12.7004 15.6157 11.9965 16.2847C11.288 15.61 10.7577 14.7701 10.453 13.8404H9.96108C10.2782 14.897 10.8618 15.8383 11.6335 16.5869C10.6164 17.4106 9.33154 17.9198 7.9209 17.9198C4.65146 17.9198 2.00098 15.2693 2.00098 12C2.00098 8.73056 4.65157 6.08008 7.9209 6.08008C9.33166 6.08008 10.6164 6.58933 11.6335 7.413C10.8509 8.17097 10.2752 9.11647 9.96108 10.1596H10.453C10.7576 9.22984 11.2879 8.39001 11.9965 7.71525C12.7003 8.38416 13.2418 9.21774 13.5501 10.1625H14.0419C13.7234 9.09984 13.1297 8.15975 12.352 7.40891C13.3715 6.5767 14.6624 6.08019 16.0812 6.08019ZM18.059 11.1964L17.6502 13.3868H18.252L18.4371 12.3841C18.6055 11.7616 18.8644 11.7648 19.0157 11.7667H19.0157C19.0237 11.7668 19.0314 11.7669 19.0388 11.7669L19.2006 11.1964C18.8999 11.1964 18.7224 11.3041 18.6298 11.4122L18.5758 11.1964H18.059ZM10.3249 10.6397H9.72927L9.31687 12.7167C9.20216 13.3122 9.6528 13.3964 9.79015 13.3964C9.92774 13.3964 10.1033 13.3733 10.2332 13.2893L10.2714 12.8465C9.92774 12.9 9.91237 12.7472 9.94299 12.5791L10.1111 11.6934H10.4165L10.5232 11.22H10.2027L10.3249 10.6397ZM11.4934 11.1895C12.5395 11.1895 12.3791 12.0599 12.3103 12.4113H11.0122C10.9456 12.8934 11.3039 12.8928 11.5413 12.8923L11.5512 12.8923C11.5548 12.8923 11.5584 12.8923 11.562 12.8923C11.7988 12.8923 12.1426 12.8616 12.2342 12.8389L12.1426 13.2892C11.9974 13.3657 11.6308 13.3963 11.3788 13.3886C10.348 13.3886 10.3632 12.4189 10.3861 12.2813C10.3868 12.2771 10.3876 12.2722 10.3885 12.2665C10.4172 12.0867 10.5604 11.1895 11.4934 11.1895ZM9.30173 11.2658L9.16438 11.7088C8.99618 11.6629 8.71373 11.6629 8.57638 11.6783C8.35492 11.7394 8.44653 11.9455 8.56875 11.9989C8.69086 12.0525 8.88181 12.1593 8.99629 12.2358C9.11089 12.3042 9.25098 12.4498 9.2025 12.7701C9.10785 13.3973 8.45595 13.3907 8.27113 13.3889C8.2624 13.3888 8.25472 13.3887 8.24818 13.3887C8.04175 13.3887 7.82053 13.3887 7.54559 13.3122L7.62957 12.8617C7.87607 12.9439 8.19047 12.9267 8.34351 12.9184L8.34362 12.9184C8.37542 12.9166 8.40025 12.9153 8.41604 12.9153C8.61587 12.9153 8.64363 12.6625 8.49251 12.587C8.45018 12.5658 8.40693 12.541 8.35896 12.5134L8.35895 12.5134C8.26842 12.4615 8.16105 12.3998 8.01125 12.3349C7.86923 12.2733 7.72107 12.1059 7.80505 11.7012C7.91305 11.1808 8.60899 11.1818 8.70967 11.1819L8.71373 11.1819C8.72437 11.1819 8.7372 11.1815 8.75207 11.1811C8.8548 11.1782 9.05491 11.1724 9.30173 11.2658ZM5.79673 10.7465L5.30046 13.3886H4.68185L5.03314 11.5255L4.20071 13.3886H3.63569L3.47524 11.4796L3.10869 13.3886H2.51318L3.02482 10.7465H3.97925L4.09385 12.3502L4.81943 10.7465H5.79673ZM5.86535 11.7317L5.95696 11.2583C6.28076 11.1947 6.4778 11.1893 6.64029 11.1849L6.64039 11.1849C6.6734 11.184 6.70498 11.1831 6.73591 11.1818C7.73609 11.1818 7.63697 11.8996 7.57586 12.1974L7.33904 13.3886H6.78177L6.70541 13.2664C6.59844 13.3428 6.51446 13.3811 6.20902 13.3964C5.90359 13.4116 5.51428 13.1826 5.58302 12.7318C5.72932 11.9333 6.56025 11.9584 6.87233 11.9679H6.87234C6.9183 11.9693 6.95301 11.9703 6.97261 11.9684C7.03477 11.6715 6.85347 11.668 6.66562 11.6644C6.64575 11.664 6.6258 11.6637 6.60607 11.6629C6.4503 11.6573 6.1898 11.6908 6.00917 11.714L6.00916 11.714L6.00906 11.714C5.95076 11.7215 5.90078 11.728 5.86535 11.7317ZM15.1509 11.2505C15.3496 11.2505 15.4562 11.2657 15.6091 11.3574L15.9987 10.9681C15.846 10.8 15.6701 10.635 15.1816 10.6168C14.7693 10.6015 14.0627 10.9024 13.8375 11.7239C13.5321 12.8389 14.2039 13.3886 14.8226 13.3886C15.1509 13.3886 15.3496 13.3428 15.548 13.2512L15.3724 12.7167C14.7614 12.9227 14.3263 12.6097 14.4256 11.976C14.5094 11.4402 14.9524 11.2505 15.1509 11.2505ZM20.6027 11.3698C20.6308 11.3993 20.6574 11.4272 20.6872 11.449L20.8473 10.6093H21.4736L20.9543 13.3887H20.313L20.2363 13.2131C20.1143 13.2971 19.9158 13.3812 19.7172 13.3887C19.2897 13.3887 18.8972 12.7845 19.0913 12.1211C19.3658 11.1814 19.8389 11.186 20.1672 11.1892C20.1857 11.1894 20.2037 11.1895 20.2213 11.1895C20.4307 11.1895 20.5225 11.2858 20.6027 11.3698ZM15.945 11.7317L16.0368 11.2583C16.3604 11.1947 16.5575 11.1893 16.7199 11.1849H16.7199C16.753 11.184 16.7846 11.1831 16.8155 11.1818C17.8159 11.1818 17.7168 11.8996 17.6557 12.1974L17.4188 13.3886H16.8616L16.7851 13.2664C16.678 13.3428 16.5943 13.3811 16.2886 13.3964C15.9832 13.4116 15.5939 13.1826 15.6627 12.7318C15.809 11.9333 16.64 11.9584 16.952 11.9679H16.9521C16.998 11.9693 17.0327 11.9703 17.0523 11.9684C17.1146 11.6715 16.9331 11.668 16.7453 11.6644C16.7254 11.664 16.7055 11.6637 16.6858 11.6629C16.5299 11.6573 16.2696 11.6907 16.089 11.714C16.0306 11.7215 15.9805 11.728 15.945 11.7317ZM12.7322 11.22L12.3274 13.3886L12.9231 13.3887L13.1062 12.3958C13.2715 11.7852 13.5317 11.7898 13.6825 11.7924H13.6825C13.692 11.7926 13.701 11.7928 13.7095 11.7928L13.8622 11.22C13.5646 11.22 13.389 11.3269 13.2972 11.4339L13.2438 11.22H12.7322Z"
                              fill="#343C54"
                          />
                        </svg>
                        <svg
                            width="50"
                            height="50"
                            viewBox="0 0 24 24"
                            fill="#343C54"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                          <path
                              d="M15.0267 8.75977C13.6066 8.75977 12.3376 9.49582 12.3376 10.8557C12.3376 12.4153 14.5883 12.523 14.5883 13.3065C14.5883 13.6364 14.2102 13.9317 13.5645 13.9317C12.6482 13.9317 11.9633 13.5191 11.9633 13.5191L11.6702 14.8914C11.6702 14.8914 12.4592 15.2399 13.5067 15.2399C15.0593 15.2399 16.281 14.4677 16.281 13.0846C16.281 11.4366 14.021 11.3321 14.021 10.6049C14.021 10.3464 14.3313 10.0633 14.9752 10.0633C15.7016 10.0633 16.2943 10.3634 16.2943 10.3634L16.5811 9.03798C16.5811 9.03798 15.9362 8.75977 15.0267 8.75977ZM2.03439 8.85981L2 9.05987C2 9.05987 2.59744 9.1692 3.13552 9.38731C3.82833 9.6374 3.8777 9.78301 3.99437 10.2352L5.26585 15.1368H6.97031L9.59614 8.85981H7.8956L6.20835 13.1275L5.51986 9.51C5.45672 9.09598 5.13688 8.85981 4.74539 8.85981H2.03439ZM10.2799 8.85981L8.94593 15.1367H10.5675L11.8969 8.85979H10.2799V8.85981ZM19.3242 8.85981C18.9331 8.85981 18.726 9.06916 18.5739 9.43498L16.1982 15.1367H17.8987L18.2277 14.1864H20.2995L20.4995 15.1367H22L20.691 8.85981H19.3242ZM19.5453 10.5556L20.0494 12.9111H18.699L19.5453 10.5556Z"
                              fill="#343C54"
                          />
                        </svg>
                      </div>
                    </div>
                    <disclosure-panel @click.prevent>
                      <div class="w-full cursor-default">
                        <checkout-credit-card/>
                      </div>
                    </disclosure-panel>
                  </div>
                </disclosure-button>
              </disclosure>
            </div>
            <div class="flex flex-col">
              <div class="flex justify-between">
                <div class="flex items-center gap-x-2">
                  <label
                      class="relative flex items-center cursor-pointer"
                      for="cash"
                  >
                    <input
                        name="payment_type"
                        type="radio"
                        checked
                        @click="closeDisclosure"
                        class="peer h-5 w-5 cursor-pointer appearance-none rounded-full border border-slate-300 checked:border-slate-400 transition-all"
                        id="cash"
                    />
                    <span
                        class="absolute bg-slate-800 w-3 h-3 rounded-full opacity-0 peer-checked:opacity-100 transition-opacity duration-200 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2"
                    >
                      </span>
                  </label>
                  <label
                      class="ml-2 text-slate-600 cursor-pointer text-sm"
                      for="cash"
                  >Cash ob delivery</label
                  >
                </div>
              </div>
            </div>
          </div>
          <div class="flex flex-col gap-y-6">
            <div class="flex gap-x-4 items-center">
              <order-coupon @coupon-applied="handleCouponApplied"/>
            </div>
            <div class="block">
              <button
                  type="button"
                  @click="handleCheckout"
                  @click.prevent="useScrolling().scrollToTop()"
                  class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
              >
                Place Order
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </section>
</template>

<style scoped>

</style>