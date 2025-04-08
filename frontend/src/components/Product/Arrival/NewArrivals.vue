<template>
  <div v-if="Object.keys(arrivals).length === 4">
    <section-title :title="'Featured'"/>
    <div class="flex justify-between mb-10">
      <p class="text-4xl font-semibold">New Arrival</p>
    </div>
    <div class="max-h-720">
      <div class="grid grid-cols-2 gap-12">
        <div class="grid grid-cols-1 relative">
          <img class="w-full" :src="arrivals[1].attributes.image_url" :alt="arrivals[1].attributes.content.title">
          <Description
              :description="arrivals[1].attributes.content?.body ?? ''"
              :link="arrivals[1].attributes.arrival_url"
              :name="arrivals[1].attributes.content.title"
          />
        </div>

        <div class="grid grid-cols-2 grid-rows-2 gap-12">
          <div class="col-span-2 h-full relative">
            <img class="w-full" :src="arrivals[2].attributes.image_url" :alt="arrivals[2].attributes.content.title">
            <Description
                :description="arrivals[2].attributes.content?.body ?? ''"
                :link="arrivals[2].attributes.arrival_url"
                :name="arrivals[2].attributes.content.title"
            />
          </div>
          <div class="h-full relative">
            <img class="w-full" :src="arrivals[3].attributes.image_url" :alt="arrivals[3].attributes.content.title">
            <Description
                :description="arrivals[3].attributes.content?.body ?? ''"
                :link="arrivals[3].attributes.arrival_url"
                :name="arrivals[3].attributes.content.title"
            />
          </div>
          <div class="h-full relative">
            <img class="w-full" :src="arrivals[4].attributes.image_url" :alt="arrivals[4].attributes.content.title">
            <Description
                :description="arrivals[4].attributes.content?.body ?? ''"
                :link="arrivals[4].attributes.arrival_url"
                :name="arrivals[4].attributes.content.title"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Description from "@/components/Product/Arrival/Description.vue";
import SectionTitle from "@/components/Default/SectionTitle.vue";
import {ContentManagementService} from "@/services/Different/ContentManagementService.js";
import {ref} from "vue";

const arrivals = ref({});

async function getArrivals() {
  await ContentManagementService.getAllArrivals()
      .then(response => {
        arrivals.value = Object.fromEntries(
            response.data.data.map(({attributes, ...rest}) => [attributes.position, {attributes, ...rest}])
        );
      })
      .catch(error => {
        if (error?.status === 404) {

        } else {
          console.error(error);
        }
      });
}

await getArrivals();
</script>

<style scoped>

</style>