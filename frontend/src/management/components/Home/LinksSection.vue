<script setup>
import LiLink from "@/management/components/Home/LiLink.vue";
import {useAuthStore} from "@/stores/useAuthStore.js";
</script>

<template>
  <div class="mt-10 grid gap-4 sm:mt-16 lg:grid-cols-3 lg:grid-rows-2">
    <div class="relative lg:row-span-2">
      <div class="absolute inset-px rounded-lg bg-white lg:rounded-l-[2rem]"></div>
      <div
          class="relative flex h-full justify-around py-4 flex-col overflow-hidden rounded-[calc(var(--radius-lg)+1px)] lg:rounded-l-[calc(2rem+1px)]">
        <div>
          <div class="px-8 sm:px-10">
            <p class="mt-2 text-lg font-medium tracking-tight text-gray-950 max-lg:text-center">
              Products
            </p>
            <p class="mt-2 max-w-lg text-sm/6 text-gray-600 max-lg:text-center">Here you can manage your products.</p>
          </div>
          <div class="px-8 pt-4 sm:pt-5 sm:px-10">
            <ul class="text-gray-500 dark:text-gray-400 font-medium">
              <li class="mb-4">
                <li-link title="List of Products" icon="pi-bars" link-name="product-list"/>
              </li>
              <li class="mb-4">
                <li-link title="Create New Product" icon="pi-plus" link-name="create-product"/>
              </li>
              <li class="mb-4">
                <li-link title="Trashed Products" icon="pi-trash" link-name="products-trashed"/>
              </li>
            </ul>
          </div>
        </div>
        <div>
          <div class="px-8 sm:px-10">
            <p class="mt-2 text-lg font-medium tracking-tight text-gray-950 max-lg:text-center">
              Categories & Brands
            </p>
            <p class="mt-2 max-w-lg text-sm/6 text-gray-600 max-lg:text-center">Comfortable management for categories
              and brands.</p>
          </div>
          <div class="px-8 pt-4 sm:pt-5 sm:px-10">
            <ul class="text-gray-500 dark:text-gray-400 font-medium">
              <li class="mb-4">
                <li-link title="Categories" icon="pi-th-large" link-name="categories-brands"/>
              </li>
              <li class="mb-4">
                <li-link title="Brands" icon="pi-apple" link-name="categories-brands"/>
              </li>
            </ul>
          </div>
        </div>
        <div>
          <div class="px-8 sm:px-10">
            <p class="mt-2 text-lg font-medium tracking-tight text-gray-950 max-lg:text-center">
              Warehouse
            </p>
            <p class="mt-2 max-w-lg text-sm/6 text-gray-600 max-lg:text-center">Simple section for viewing your products
              in warehouse.</p>
          </div>
          <div class="px-8 pt-4 sm:pt-5 sm:px-10">
            <ul class="text-gray-500 dark:text-gray-400 font-medium">
              <li class="mb-4">
                <li-link title="Warehouse" icon="pi-warehouse" link-name="warehouse"/>
              </li>
              <li class="mb-4">
                <li-link title="Discounts" icon="pi-percentage" link-name="discounts"/>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div
          class="pointer-events-none absolute inset-px rounded-lg ring-1 shadow-sm ring-black/5 lg:rounded-l-[2rem]"></div>
    </div>
    <div class="relative max-lg:row-start-1">
      <div class="absolute inset-px rounded-lg bg-white max-lg:rounded-t-[2rem]"></div>
      <div
          class="relative flex h-full flex-col overflow-hidden rounded-[calc(var(--radius-lg)+1px)] max-lg:rounded-t-[calc(2rem+1px)]">
        <div class="px-8 pt-8 sm:px-10 sm:pt-10">
          <p class="mt-2 text-lg font-medium tracking-tight text-gray-950 max-lg:text-center">Clients</p>
          <p class="mt-2 max-w-lg text-sm/6 text-gray-600 max-lg:text-center">Section for control and viewing
            customers.</p>
        </div>
        <div class="px-8 pt-4 sm:pt-5 sm:px-10">
          <ul class="text-gray-500 dark:text-gray-400 font-medium">
            <li class="mb-4">
              <li-link title="Regular Customers" icon="pi-verified" link-name="regular-customers"/>
            </li>
            <li class="mb-4">
              <li-link title="Guests" icon="pi-users" link-name="guests"/>
            </li>
            <li class="mb-4" v-if="useAuthStore().isSuperManager">
              <li-link title="Managers" icon="pi-user-edit" link-name="managers"/>
            </li>
            <li class="mb-4">
              <li-link title="Coupons" icon="pi-ticket" link-name="coupons"/>
            </li>
          </ul>
        </div>
      </div>
      <div
          class="pointer-events-none absolute inset-px rounded-lg ring-1 shadow-sm ring-black/5 max-lg:rounded-t-[2rem]"></div>
    </div>
    <div class="relative max-lg:row-start-3 lg:col-start-2 lg:row-start-2">
      <div class="absolute inset-px rounded-lg bg-white"></div>
      <div class="relative flex h-full flex-col overflow-hidden rounded-[calc(var(--radius-lg)+1px)]">
        <div class="px-8 pt-8 sm:px-10 sm:pt-10">
          <p class="mt-2 text-lg font-medium tracking-tight text-gray-950 max-lg:text-center">Content Management</p>
          <p class="mt-2 max-w-lg text-sm/6 text-gray-600 max-lg:text-center">CMS section - is a section, where you can
            control your
            public content of shop.</p>
        </div>
        <div class="px-8 pt-4 sm:pt-5 sm:px-10">
          <ul class="text-gray-500 dark:text-gray-400 font-medium">
            <li class="mb-4">
              <li-link title="Slider" icon="pi-sliders-h" link-name="content-slider"/>
            </li>
            <li class="mb-4">
              <li-link title="New Arrivals" icon="pi-cart-arrow-down" link-name="content-arrivals"/>
            </li>
          </ul>
        </div>
      </div>
      <div class="pointer-events-none absolute inset-px rounded-lg ring-1 shadow-sm ring-black/5"></div>
    </div>
    <div class="relative lg:row-span-2">
      <div class="absolute inset-px rounded-lg bg-white max-lg:rounded-b-[2rem] lg:rounded-r-[2rem]"></div>
      <div
          class="relative flex h-full flex-col overflow-hidden rounded-[calc(var(--radius-lg)+1px)] max-lg:rounded-b-[calc(2rem+1px)] lg:rounded-r-[calc(2rem+1px)]">
        <div class="px-8 pt-8 pb-3 sm:px-10 sm:pt-10 sm:pb-0">
          <p class="mt-2 text-lg font-medium tracking-tight text-gray-950 max-lg:text-center">Orders</p>
          <p class="mt-2 max-w-lg text-sm/6 text-gray-600 max-lg:text-center">Comfortable section for control, manage
            and view customers orders.</p>
        </div>
        <div class="px-8 pt-4 sm:pt-5 sm:px-10">
          <ul class="text-gray-500 dark:text-gray-400 font-medium">
            <li class="mb-4">
              <li-link title="Pending Orders" icon="pi-clock" link-name="pending-orders"/>
            </li>
            <li class="mb-4">
              <li-link title="Cancelled Orders" icon="pi-times" link-name="cancelled-orders"/>
            </li>
            <li class="mb-4">
              <li-link title="Delivered Orders" icon="pi-check-circle" link-name="delivered-orders"/>
            </li>
            <li class="mb-4">
              <li-link title="Refunded Orders" icon="pi-sync" link-name="refunded-orders"/>
            </li>
          </ul>
        </div>
      </div>
      <div
          class="pointer-events-none absolute inset-px rounded-lg ring-1 shadow-sm ring-black/5 max-lg:rounded-b-[2rem] lg:rounded-r-[2rem]"></div>
    </div>
  </div>
</template>

<style scoped>

</style>