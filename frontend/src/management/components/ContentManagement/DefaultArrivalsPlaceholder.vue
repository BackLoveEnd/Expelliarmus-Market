<script setup>

import Description from '@/components/Product/Arrival/Description.vue'

const props = defineProps({
  position: Number,
  image: String,
  content: Object
})
</script>

<template>
  <div>
    <h2 class="text-l font-semibold text-center">Position {{ position }}</h2>
    <img :src="image" :alt="`${position} position`"/>
    <Description
        :description="content.body"
        :name="content.title"
        :link="content.url"/>
  </div>
</template>

<style scoped>

</style>