<template>
  <default-container>
    <div class="py-20">
      <div class="mx-auto max-w-2xl px-6 lg:max-w-7xl lg:px-8">
        <h2 class="text-center text-base/7 font-semibold text-indigo-600">Welcome back, manager.</h2>
        <stats/>
        <links-section/>
      </div>
    </div>
  </default-container>
</template>

<script>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import Stats from "@/management/components/Home/Stats.vue";
import LinksSection from "@/management/components/Home/LinksSection.vue";

export default {
  components: {
    LinksSection,
    Stats,
    DefaultContainer
  }
};
</script>

<style scoped>

</style>