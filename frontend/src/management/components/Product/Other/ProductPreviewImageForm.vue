<script setup>
import { ref, watch } from "vue";

const previewImage = ref({
  url: "",
  file: null,
});

const props = defineProps({
  existsPreviewImage: String,
  modelValue: null,
});

const emit = defineEmits(["update:modelValue"]);

previewImage.value.url = props.existsPreviewImage || "";

watch(
  () => props.existsPreviewImage,
  (newVal) => {
    if (!previewImage.value.file) {
      previewImage.value.url = newVal;
    }
  },
);

function handlePreviewFileChange(event) {
  const file = event.target.files[0];
  if (file) {
    const fileUrl = URL.createObjectURL(file);
    previewImage.value = { file, url: fileUrl };
    emit("update:modelValue", previewImage.value);
  }
}
</script>

<template>
  <div class="w-full xl:w-auto">
    <label for="preview-image" class="block text-base font-medium">
      Preview Image (the best option is a square image)
    </label>
    <input
      type="file"
      id="preview-image"
      accept="image/*"
      class="mt-1 block w-full"
      @change="handlePreviewFileChange"
    />
    <img
      v-if="previewImage.url"
      :src="previewImage.url"
      alt="Preview"
      class="mt-4 rounded-md w-44 h-44 object-cover"
    />
  </div>
</template>
