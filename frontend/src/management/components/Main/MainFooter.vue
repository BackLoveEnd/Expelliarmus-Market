<template>
  <footer class="mt-auto fixed w-full bottom-0 z-10">
    <div
        class="container mx-auto bg-yellow-100 px-8 pt-5 pb-3 rounded-t-lg shadow-light-mode dark:shadow-dark-mode"
    >
      <nav
          class="flex flex-col gap-y-4 md:gap-y-4 lg:flex-row lg:gap-y-0 items-center justify-between dark:text-black relative"
      >
        <div v-show="isContentVisible" class="text-center content">
          <router-link to="" class="font-bold text-xl">
            Expelliarmus<br/><span class="text-base font-normal"
          >Management</span
          >
          </router-link>
        </div>

        <div v-show="isContentVisible" class="content">
          <ul
              class="list-none flex flex-col lg:flex-row items-center lg:gap-x-14 md:gap-y-2"
          >
            <li
                class="hover:underline underline-offset-4 decoration-2 mb-2 md:mb-0"
            >
              <router-link to="/management">
                <i class="pi pi-home"></i>
                Home
              </router-link>
            </li>
            <li @mouseenter="handleMenuEnter" @mouseleave="handleMenuLeave">
              <custom-menu :links="clientLinks" drop-to-up>
                <template v-slot:menu-button="{ isOpen }">
                  <span class="hover:underline underline-offset-4 decoration-2 mb-2 md:mb-0">
                    <i class="pi pi-users"></i>
                    Clients
                  </span>
                </template>
              </custom-menu>
            </li>
            <li @mouseenter="handleMenuEnter" @mouseleave="handleMenuLeave">
              <custom-menu :links="ordersLinks" drop-to-up>
                <template v-slot:menu-button="{ isOpen }">
                  <span class="hover:underline underline-offset-4 decoration-2 mb-2 md:mb-0">
                    <i class="pi pi-shopping-bag"></i>
                    Orders
                  </span>
                </template>
              </custom-menu>
            </li>
            <li @mouseenter="handleMenuEnter" @mouseleave="handleMenuLeave">
              <custom-menu :links="productLinks" drop-to-up>
                <template v-slot:menu-button="{ isOpen }">
                  <span class="hover:underline underline-offset-4 decoration-2 mb-2 md:mb-0">
                    <i class="pi pi-shopping-cart"></i>
                    Products
                  </span>
                </template>
              </custom-menu>
            </li>
            <li @mouseenter="handleMenuEnter" @mouseleave="handleMenuLeave">
              <custom-menu :links="warehouseLinks" drop-to-up>
                <template v-slot:menu-button="{ isOpen }">
                  <span class="hover:underline underline-offset-4 decoration-2 mb-2 md:mb-0">
                    <i class="pi pi-objects-column"></i>
                    Warehouse
                  </span>
                </template>
              </custom-menu>
            </li>
            <li @mouseenter="handleMenuEnter" @mouseleave="handleMenuLeave">
              <custom-menu :links="contentLinks" drop-to-up>
                <template v-slot:menu-button="{ isOpen }">
                  <span class="hover:underline underline-offset-4 decoration-2 mb-2 md:mb-0">
                    <i class="pi pi-objects-column"></i>
                    Content Management
                  </span>
                </template>
              </custom-menu>
            </li>
          </ul>
        </div>

        <div
            v-show="isContentVisible"
            class="flex-shrink-0 content mb-4 md:mb-0 flex gap-x-4 items-center"
        >
          <dark-mode></dark-mode>
          <button class="flex items-center gap-x-2" @click="handleLogout">
            <i class="pi pi-sign-out"></i>
            Log Out
          </button>
        </div>
      </nav>
    </div>
  </footer>
</template>

<script setup>
import CustomMenu from "@/components/Default/Menu.vue";
import DarkMode from "@/management/components/Main/DarkMode.vue";
import {onMounted, onUnmounted, ref} from "vue";
import {onBeforeRouteUpdate, useRouter} from "vue-router";
import {useAuthStore} from "@/stores/useAuthStore.js";

const isContentVisible = ref(false);

const isMenuOpen = ref(false);

const router = useRouter();

const auth = useAuthStore();

const contentLinks = ref([
  {url: "/management/content/slider", name: "Slider", svg: 'pi pi-sliders-h'},
  {url: "/management/content/new-arrivals", name: "New Arrivals", svg: 'pi pi-cart-arrow-down'},
]);

const productLinks = ref([
  {url: "/management/products", name: "All Products", svg: 'pi pi-align-justify'},
  {url: "/management/products/trashed", name: "Trashed Products", svg: 'pi pi-trash'},
  {url: "/management/products/create", name: "Add Product", svg: 'pi pi-plus'},
  {url: "/management/categories-and-brands", name: "Categories & Brands", svg: 'pi pi-th-large'},
]);

const ordersLinks = ref([
  {url: "/management/orders/pending", name: "Pending Orders", svg: 'pi pi-clock'},
  {url: "/management/orders/cancelled", name: "Cancelled Orders", svg: 'pi pi-times'},
  {url: "/management/orders/delivered", name: "Delivered Orders", svg: 'pi pi-check-circle'},
  {url: "/management/orders/refunded", name: "Refunded Orders", svg: 'pi pi-sync'},
]);

const clientLinks = ref([
  {url: "/management/clients/regular", name: "Regular Customers", svg: "pi pi-verified", onlyForSuperManager: false},
  {url: "/management/clients/guests", name: "Guests", svg: "pi pi-user", onlyForSuperManager: false},
  {url: "/management/managers", name: "Managers", svg: "pi pi-user-edit", onlyForSuperManager: true},
  {url: "/management/clients/coupons", name: "Coupons", svg: "pi pi-ticket", onlyForSuperManager: false},
]);

const warehouseLinks = ref([
  {url: "/management/warehouse", name: "Products", svg: "pi pi-align-justify"},
  {url: "/management/products/discounts", name: "Discounts", svg: "pi pi-percentage"}
]);

const handleMouseMove = (event) => {
  if (isMenuOpen.value) return;

  const windowHeight = window.innerHeight;

  const height = isContentVisible.value ? 100 : 50;

  isContentVisible.value = event.clientY > windowHeight - height;
};

const handleMenuEnter = () => {
  isMenuOpen.value = true;
};

const handleMenuLeave = () => {
  isMenuOpen.value = false;
};

const handleLogout = async () => {
  await auth.logout(true);
  await router.push({name: "manager-login"});
  router.go(0);
};

onMounted(() => {
  window.addEventListener("mousemove", handleMouseMove);
});

onUnmounted(() => {
  window.removeEventListener("mousemove", handleMouseMove);
});

onBeforeRouteUpdate(() => {
  isContentVisible.value = false;
});
</script>

<style scoped>
footer {
  position: fixed;
  bottom: 0;
  width: 100%;
  transition: transform 0.5s ease-in-out,
  opacity 0.5s ease-in-out;
}

.footer-hidden {
  transform: translateY(100%);
  opacity: 0;
}

.footer-visible {
  transform: translateY(0);
  opacity: 1;
}
</style>
