<template>
  <section class="w-full">
    <span class="text-xl font-medium text-[#db4444]">Edit your profile</span>
  </section>
  <section class="space-y-8">
    <user-info></user-info>
    <password-reset></password-reset>
  </section>
</template>

<script setup>
import UserInfo from "@/components/User/Profile/UserInfo.vue";
import PasswordReset from "@/components/User/Profile/PasswordReset.vue";
</script>

<style scoped></style>
