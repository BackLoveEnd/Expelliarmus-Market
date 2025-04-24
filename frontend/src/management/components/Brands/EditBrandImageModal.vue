<script setup>
import {ref, watch} from "vue";
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {BrandsService} from "@/services/Brand/BrandsService.js";
import {useToastStore} from "@/stores/useToastStore.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";

const props = defineProps({
  isModalOpen: Boolean,
  editBrandImageData: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(["modal-close"]);

const toastStore = useToastStore();

const imageFile = ref(null);
const imagePreview = ref("");

watch(
    () => props.editBrandImageData,
    (val) => {
      if (val?.logo) {
        imagePreview.value = val.logo;
      }
    },
    {immediate: true}
);

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    imageFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

const closeModal = () => {
  emit("modal-close");
};

const handleSave = async () => {
  await BrandsService.uploadImage(imageFile.value, props.editBrandImageData.id)
      .then((response) => {
        closeModal();

        toastStore.showToast(response?.data?.message, defaultSuccessSettings);
      })
      .catch((e) => {
        if (e?.status === 422) {
          toastStore.showToast(e?.response?.data?.message, defaultErrorSettings);
        } else {
          toastStore.showToast('Failed to upload brand logo. Please try again or contact us.', defaultErrorSettings);
        }
      });
};
</script>

<template>
  <DefaultModal
      :isActive="isModalOpen"
      @close-modal="closeModal"
      max-width="max-w-2xl"
  >
    <template #modalHeading>Create New Brand</template>

    <template #modalBody>
      <div class="flex flex-col items-center gap-4">
        <div v-if="imagePreview" class="w-40 h-40 rounded-md overflow-hidden border">
          <img
              :src="imagePreview"
              alt="Preview"
              class="w-full h-full object-contain"
          />
        </div>
        <label
            class="cursor-pointer px-6 py-3 border-2 border-dashed border-gray-400 rounded-md text-center hover:bg-gray-50"
        >
          <input type="file" accept="image/*" class="hidden" @change="handleFileChange"/>
          <span>Click to upload or drag an image</span>
        </label>
      </div>
    </template>

    <template #modalFooter>
      <button
          type="button"
          @click="handleSave"
          class="px-4 py-2 bg-blue-500 text-white rounded-md"
      >
        Upload
      </button>
      <button
          type="button"
          @click="closeModal"
          class="ml-2 px-4 py-2 bg-gray-300 rounded-md"
      >
        Cancel
      </button>
    </template>
  </DefaultModal>
</template>

<style scoped></style>
