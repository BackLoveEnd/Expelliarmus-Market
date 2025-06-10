<template>
  <div class="flex justify-between relative">
    <div class="mt-10">
      <main-categories-menu/>
    </div>
    <div class="flex items-stretch">
      <div class="h-auto w-px bg-gray-300"></div>
      <slider :slides="slidesPreview" class="mt-10 ml-10"/>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Slider from '@/components/Default/Slider.vue'
import MainCategoriesMenu from '@/shop/components/Categories/MainCategoriesMenu.vue'
import { ContentManagementService } from '@/services/Different/ContentManagementService.js'

const images = ref([])

onMounted(async () => {
  await ContentManagementService.getAllSlides()
      .then((response) => {
        const slides = response.data.data

        if (slides && slides.length) {
          images.value = slides.map((slide, index) => ({
            preview: slide.attributes.image,
            content_url: slide.attributes.content_url,
          }))
        } else {
          images.value = [
            {
              preview: 'https://dummyimage.com/1080x400/000/fff',
              content_url: '/',
            },
          ]
        }
      })
      .catch(() => {
        images.value = [
          {
            preview: 'https://dummyimage.com/1080x400/000/fff',
            content_url: '/',
          },
        ]
      })
})

const slidesPreview = computed(() =>
    images.value.map((image) => image.preview),
)

</script>

<style scoped></style>
