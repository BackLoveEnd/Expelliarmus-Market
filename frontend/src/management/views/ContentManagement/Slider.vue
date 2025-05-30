<script setup>
import {computed, onMounted, ref} from "vue";
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import {ContentManagementService} from "@/services/Different/ContentManagementService.js";
import Slider from "@/components/Default/Slider.vue";
import {useRouter} from "vue-router";
import {useToastStore} from "@/stores/useToastStore.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";

const images = ref([]);

const router = useRouter();

const toast = useToastStore();

const fetchSlides = async () => {
  await ContentManagementService.getAllSlides()
      .then((response) => {
        const slides = response.data.data;

        if (slides && slides.length) {
          images.value = slides.map((slide, index) => ({
            id: slide.id,
            file: null,
            tmpImage: null,
            preview: slide.attributes.image,
            order: index + 1,
            content_url: slide.attributes.content_url,
          }));
        } else {
          images.value = [
            {
              file: null,
              preview: null,
              tmpImage: null,
              order: 1,
              content_url: "",
              image_url: null,
            },
          ];
        }
      })
      .catch(() => {
      });
};

const handleFileUpload = (event, index) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      images.value[index].file = file;
      images.value[index].tmpImage = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const addNewSlideBelow = (index) => {
  const newSlide = {
    file: null,
    preview: null,
    tmpImage: null,
    order: images.value[index].order + 1,
    content_url: "",
    image_url: null,
  };

  images.value.splice(index + 1, 0, newSlide);

  images.value.forEach((slide, i) => {
    slide.order = i + 1;
  });
};

const deleteSlide = async (image, index) => {
  if (image?.id) {
    await ContentManagementService.deleteSlide(image.id)
        .then((response) => {
          if (response?.status === 200) {
            images.value.splice(index, 1);
            images.value.forEach((image, i) => {
              image.order = i + 1;
            });
            toast.showToast(response?.data?.message, defaultSuccessSettings);
          }
        })
        .catch((e) => {
          if (e.response?.status === 500) {
            toast.showToast(
                "Unknown error. Try again or contact us.",
                defaultErrorSettings,
            );
          }
        });
  } else {
    images.value.splice(index, 1);
    images.value.forEach((image, i) => {
      image.order = i + 1;
    });
  }
};

const saveSlides = async () => {
  const payload = images.value.map(
      ({id, file, order, content_url, preview}) => ({
        slide_id: id,
        image: file,
        order,
        content_url,
        image_url: preview ?? null,
      }),
  );

  await ContentManagementService.uploadSliderContent(payload)
      .then((response) => {
        if (response?.status === 200) {
          toast.showToast(response?.data?.message, defaultSuccessSettings);
          router.go(0);
        }
      })
      .catch((e) => {
        console.log(e);
      });
};

const slidesPreview = computed(() =>
    images.value.map((image) => image.preview ?? image.tmpImage),
);

onMounted(fetchSlides);
</script>

<template>
  <default-container>
    <div class="my-14 space-y-8">
      <section class="container mx-auto">
        <h1 class="font-semibold text-4xl mb-4">Slider Management</h1>
        <div class="flex flex-col items-center mt-12 space-y-8">
          <h2 class="text-xl font-semibold">Slides</h2>
          <div
              v-for="(image, index) in images"
              :key="index"
              class="shadow-md p-4 rounded-lg space-y-2"
          >
            <img
                v-if="image.tmpImage || image.preview"
                :src="image.tmpImage ?? image.preview"
                alt="Slide Preview"
                class="w-[1080px] h-[400px] object-cover mb-2"
            />
            <input
                type="file"
                @change="(e) => handleFileUpload(e, index)"
                accept="image/*"
            />
            <div class="flex flex-col">
              <label :for="`content-${index}`">Content URL</label>
              <input
                  :id="`content-${index}`"
                  v-model="image.content_url"
                  placeholder="Slide URL"
                  class="mt-2 p-1 border rounded w-full"
              />
            </div>
            <div class="flex justify-between">
              <p class="mt-2">Order: {{ image.order }}</p>
              <div class="flex gap-2">
                <button
                    @click="addNewSlideBelow(index)"
                    class="bg-blue-500 text-white px-3 py-1 rounded"
                >
                  Add Below
                </button>
                <button
                    @click="deleteSlide(image, index)"
                    class="bg-red-500 text-white px-3 py-1 rounded"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
          <div class="flex gap-4 mt-4">
            <button
                @click="saveSlides"
                class="bg-gray-500 text-white px-4 py-2 rounded"
            >
              Save
            </button>
          </div>
        </div>
      </section>
      <hr/>
      <section
          class="container mx-auto space-y-4"
          v-if="
          (slidesPreview.length === 1 && slidesPreview[0] !== null) ||
          slidesPreview.length !== 1
        "
      >
        <h2 class="text-xl font-semibold text-center">Slider Preview</h2>
        <div class="flex justify-center">
          <slider :slides="slidesPreview"/>
        </div>
      </section>
    </div>
  </default-container>
</template>

<style scoped>
button {
  transition: background-color 0.3s;
}

button:hover {
  opacity: 0.9;
}
</style>
