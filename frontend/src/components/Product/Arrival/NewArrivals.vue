<template>
  <div class="grid grid-cols-2 gap-12">
    <div class="grid grid-cols-1 relative">
      <img class="w-full" src="https://dummyimage.com/744x720/000/fff" alt="First arrival">
      <Description :description="'Black and White version of the PS5 coming out on sale.'" :name="'Playstation 5'"/>
    </div>

    <div class="grid grid-cols-2 grid-rows-2 gap-12">
      <div class="col-span-2 h-full relative">
        <img src="https://dummyimage.com/744x336/000/fff" alt="Second arrival">
        <Description :description="'Black and White version of the PS5 coming out on sale.'" :name="'Playstation 5'"/>
      </div>
      <div class="h-full relative">
        <img src="https://dummyimage.com/348x336/000/fff" alt="Third arrival">
        <Description :description="'Black and White version of the PS5 coming out on sale.'" :name="'Playstation 5'"/>
      </div>
      <div class="h-full relative">
        <img src="https://dummyimage.com/348x336/000/fff" alt="Fourth arrival">
        <Description :description="'Black and White version of the PS5 coming out on sale.'" :name="'Playstation 5'"/>
      </div>
    </div>
  </div>
</template>

<script>

import Description from "@/components/Product/Arrival/Description.vue";

export default {
  components: {Description}
}


</script>

<style scoped>

</style>