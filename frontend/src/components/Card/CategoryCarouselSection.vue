<template>
  <div class="flex justify-between relative">
    <div class="mt-10 w-3/6">
      <!--      <ul class="space-y-4 text-left">
              <li
                  v-for="(category, index) in categories"
                  :key="index"
                  class="text-lg"
              >
                <button
                    href="#"
                    @click.prevent="selectCategory(index)"
                    class="text-black hover:underline underline-offset-4"
                >
                  {{ category }}
                </button>
              </li>
            </ul>-->
      <main-categories-menu/>
    </div>

    <div class="h-auto w-px bg-gray-300"></div>

    <slider :slides="slides" class="mt-10 ml-10"/>
  </div>
</template>

<script setup>
import {ref} from "vue";
import Slider from "@/components/Default/Slider.vue";
import MainCategoriesMenu from "@/shop/components/Categories/MainCategoriesMenu.vue";

const slides = ref([
  "https://dummyimage.com/1080x400/000/fff",
  "https://dummyimage.com/1080x400/000/fff",
  "https://dummyimage.com/1080x400/000/fff",
]);

const categories = ref([
  "Category 1adadadada",
  "Category 2",
  "Category 3",
  "Category 4",
  "Category 5",
  "Category 6",
  "Category 7",
  "Category 8",
]);
</script>

<style scoped></style>
