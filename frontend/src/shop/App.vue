<template>
  <main-header/>
  <router-view/>
  <back-to-top/>
  <main-footer/>
</template>

<script setup>
import {RouterView} from 'vue-router'
import MainHeader from "@/components/Default/Header/MainHeader.vue";
import MainFooter from "@/components/Default/MainFooter.vue";
import BackToTop from "@/components/Default/BackToTop.vue";
</script>

<style scoped>

</style>
