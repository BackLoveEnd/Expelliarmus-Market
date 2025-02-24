<template>
  <tab-group
      :selectedIndex="selectedTab"
      as="div"
      class="grid grid-cols-1 md:grid-cols-4 gap-6 md:gap-12"
  >
    <tab-list id="tab-list" class="md:col-span-1 flex flex-col gap-y-6">
      <Tab
          v-for="(image, index) in images"
          :key="index"
          class="w-44 h-40 bg-[#f3f4f6] rounded-md relative flex justify-center items-center focus:ring-8 focus:ring-[#e29e32]"
          @click="setMainImage(index)"
      >
        <img
            v-if="image.image_url"
            :src="image.image_url"
            alt="Uploaded image"
            class="rounded-md max-w-full max-h-full"
        />
        <input
            type="file"
            class="absolute inset-0 opacity-0 cursor-pointer"
            accept="image/*"
            @change="handleFileChange($event, index)"
        />

        <button
            type="button"
            class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex justify-center items-center text-sm"
            @click.stop="deleteImage(index)"
        >
          <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-6"
          >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 18 18 6M6 6l12 12"
            />
          </svg>
        </button>
      </Tab>

      <Tab
          v-if="images.length < 4"
          class="w-44 h-40 bg-[#f3f4f6] rounded-md relative flex justify-center items-center focus:ring-8 focus:ring-[#e29e32]"
      >
        <input
            type="file"
            class="absolute inset-0 opacity-0 cursor-pointer"
            accept="image/*"
            @change="handleFileChange($event)"
        />
        <div
            class="rounded-md bg-gray-200 flex justify-center items-center flex-col w-full h-full"
        >
          <img src="/default/add.png" alt="Add" class="w-12 h-12"/>
          <span class="dark:text-black">Add new photo</span>
        </div>
      </Tab>
    </tab-list>

    <tab-panels
        class="md:col-span-3 rounded-md bg-[#f3f4f6] max-w-xl max-h-712 overflow-hidden"
    >
      <template v-if="images.length > 0">
        <tab-panel
            v-for="(image, index) in images"
            :key="index"
            class="flex justify-center items-center h-full"
        >
          <img
              v-if="image.image_url"
              :src="image.image_url"
              alt="Main image"
              class="rounded-md max-w-full max-h-full"
          />
        </tab-panel>
      </template>
      <template v-else>
        <tab-panel class="flex justify-center items-center h-full">
          <img
              src="https://dummyimage.com/576x712/000/fff"
              alt="Main image"
              class="rounded-md max-w-full max-h-full"
          />
        </tab-panel>
      </template>
    </tab-panels>
  </tab-group>
</template>

<script setup>
import {Tab, TabGroup, TabList, TabPanel, TabPanels} from "@headlessui/vue";
import {nextTick, onMounted, ref, watch} from "vue";

const images = ref([]);
const selectedTab = ref(0);

const emit = defineEmits(["update:modelValue"]);
const props = defineProps({
  modelValue: null,
  existsImages: Array,
});

onMounted(() => {
  if (props.existsImages?.length) {
    images.value = props.existsImages.map((image) => ({...image}));
  }
});

watch(
    images,
    () => {
      images.value.forEach((image, index) => {
        image.order = index + 1;
      });
      emit("update:modelValue", images.value);
    },
    {deep: true},
);

function handleFileChange(event, index = null) {
  const file = event.target.files[0];
  if (!file) return;

  const fileUrl = URL.createObjectURL(file);

  if (index !== null) {
    images.value[index] = {file, image_url: fileUrl, order: index + 1};
  } else if (images.value.length < 4) {
    images.value.push({file, image_url: fileUrl, order: images.value.length + 1});
    selectedTab.value = images.value.length - 1;
  }

  nextTick(() => {
    const lastTab = document.querySelector(
        `#tab-list > *:nth-child(${images.value.length})`,
    );
    lastTab?.focus();
  });
}

function setMainImage(index) {
  selectedTab.value = index;
}

function deleteImage(index) {
  images.value.splice(index, 1);

  images.value.forEach((image, i) => {
    image.order = i + 1;
  });

  if (images.value.length === 0) {
    selectedTab.value = 0;
  } else if (selectedTab.value >= images.value.length) {
    selectedTab.value = images.value.length - 1;
  }
}
</script>

