<template>
  <div class="flex justify-center bg-black text-white py-3">
    <span class="text-sm">
      Summer Sale For All Swim Suits And Free Express Delivery - OFF 50%!
      <a href="#" class="font-bold underline">ShopNow</a>
    </span>
  </div>
  <header class="sticky top-0 bg-white z-10">
    <nav
        class="container mx-auto flex justify-between items-center max-h-24 pt-9 pb-4"
    >
      <div>
        <router-link
            @click.prevent="scrollToTop"
            class="text-2xl font-bold logo"
            to="/"
        >Expelliarmus Shop
        </router-link>
      </div>
      <div>
        <ul class="flex space-x-16 text-base">
          <li class="hover:underline underline-offset-4 decoration-2">
            <router-link @click.prevent="scrollToTop" to="/">Home</router-link>
          </li>
          <li class="hover:underline underline-offset-4 decoration-2">
            <router-link @click.prevent="scrollToTop" to="/contact"
            >Contact
            </router-link>
          </li>
          <li class="hover:underline underline-offset-4 decoration-2">
            <router-link @click.prevent="scrollToTop" to="/about-us"
            >About
            </router-link>
          </li>
          <li class="hover:underline underline-offset-4 decoration-2">
            <router-link
                v-if="!auth.isRegularUser"
                @click.prevent="scrollToTop"
                to="/sign-up"
            >Sign up
            </router-link>
            <button
                @click="logout"
                type="button"
                class="hover:underline underline-offset-4 decoration-2"
                v-else
            >
              Log out
            </button>
          </li>
        </ul>
      </div>
      <div class="flex items-center space-x-4">
        <search-header></search-header>
        <wishlist-header></wishlist-header>
        <cart-header></cart-header>
        <custom-menu v-if="auth.isRegularUser" :links="accountMenuLinks">
          <template v-slot:menu-button="{ isOpen }">
            <div
                :class="{ 'bg-[#db4444] text-white': isOpen }"
                class="w-full justify-center rounded-full px-1 py-1"
            >
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
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                />
              </svg>
            </div>
          </template>
          <template v-slot:logout="{ active }">
            <button
                :class="[
                active ? 'bg-[#db4444] text-white' : 'text-gray-900',
                'group justify-between flex w-full items-center rounded-md px-2 py-2 text-sm',
              ]"
                @click="logout"
            >
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
                    d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15"
                />
              </svg>
              Log Out
            </button>
          </template>
        </custom-menu>
      </div>
    </nav>
    <hr class="h-[2px] bg-gray-300 border-0"/>
  </header>
</template>

<script setup>
import CustomMenu from "@/components/Default/Menu.vue";
import CartHeader from "@/components/Default/Header/CartHeader.vue";
import WishlistHeader from "@/components/Default/Header/WishlistHeader.vue";
import SearchHeader from "@/components/Default/Header/SearchHeader.vue";
import {ref} from "vue";
import {useScrolling} from "@/composables/useScrolling.js";
import {useAuthStore} from "@/stores/useAuthStore.js";
import {useRouter} from "vue-router";

const auth = useAuthStore();

const {scrollToTop} = useScrolling();

const router = useRouter();

const accountMenuLinks = ref([
  {url: "/account?tab=0", name: "Manage My Account", svg: "pi pi-user"},
  {url: "/account?tab=1", name: "My Orders", svg: "pi pi-shopping-bag"},
  {url: "/account?tab=3", name: "My Coupons", svg: "pi pi-ticket"},
]);

async function logout() {
  await auth.logout();
  await router.push({name: "home"});
  router.go(0);
}
</script>

<style scoped></style>
