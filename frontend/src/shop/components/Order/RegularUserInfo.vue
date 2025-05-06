<script setup>
import {useAuthStore} from "@/stores/useAuthStore.js";

const auth = useAuthStore();
</script>

<template>
  <div class="max-w-md p-6 bg-white border border-gray-200 rounded-2xldark:bg-gray-800 dark:border-gray-700">
    <div class="flex items-center justify-between mb-4">
      <h5 class="text-xl font-semibold text-gray-900 dark:text-white">
        Your Info
      </h5>
      <router-link to="/account?tab=0" class="text-sm text-blue-600 hover:underline dark:text-blue-400">
        Change
      </router-link>
    </div>

    <div class="space-y-3 text-sm text-gray-800 dark:text-gray-300">
      <div>
        <span class="font-medium text-gray-500 dark:text-gray-400">First name:</span><br/>
        <span>{{ auth.firstName }}</span>
      </div>
      <div>
        <span class="font-medium text-gray-500 dark:text-gray-400">Last name:</span><br/>
        <span v-if="auth.lastName">{{ auth.lastName }}</span>
        <span v-else class="flex items-center gap-x-2 text-red-500">
          <i class="pi pi-info-circle"></i>
          Add last name in your profile.
        </span>
      </div>

      <div>
        <span class="font-medium text-gray-500 dark:text-gray-400">Address:</span><br/>
        <span v-if="auth.address">{{ auth.address }}</span>
        <span v-else class="flex items-center gap-x-2 text-red-500">
          <i class="pi pi-info-circle"></i>
          Add address in your profile.
        </span>
      </div>

      <div>
        <span class="font-medium text-gray-500 dark:text-gray-400">Phone:</span><br/>
        <span v-if="auth.phone_original">{{ auth.phone_original }}</span>
        <span v-else class="flex items-center gap-x-2 text-red-500">
          <i class="pi pi-info-circle"></i>
          Add phone number in your profile.
        </span>
      </div>
    </div>
  </div>
</template>
