<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {BrandsService} from "@/services/Brand/BrandsService.js";
import {useToastStore} from "@/stores/useToastStore.js";
import successCategorySettings from "@/management/components/Toasts/Category/successCategorySettings.js";
import failedCategorySettings from "@/management/components/Toasts/Category/failedCategorySettings.js";

const props = defineProps({
  brandData: Object,
  isModalOpen: false,
});

const toast = useToastStore();

const emit = defineEmits(["modal-close", "brand-deleted"]);

const closeModal = () => {
  emit("modal-close");
};

const deleteBrand = async () => {
  await BrandsService.deleteBrand(props.brandData.id)
      .then((response) => {
        if (response?.status === 200) {
          emit("brand-deleted", props.brandData.id);
          closeModal();
          toast.showToast(
              "Brand was deleted successfully.",
              successCategorySettings,
          );
        }
      })
      .catch((e) => {
        toast.showToast(
            "Failed to delete brand. Try again or contact us.",
            failedCategorySettings,
        );
      });
};
</script>

<template>
  <DefaultModal
      :isActive="isModalOpen"
      @close-modal="closeModal"
      max-width="max-w-2xl"
  >
    <template #modalHeading
    >Are you sure you want to delete brand - {{ brandData.brand_name }}?
    </template>
    <template #modalBody>
      <div class="my-8 flex flex-col gap-2 text-center">
        <span class="text-sm"
        >Deleting this brands means, that all products of this brands will be
          unpublished.
        </span>
        <span class="text-sm"
        >You will be able to add brand again to such products.</span
        >
      </div>
    </template>
    <template #modalFooter>
      <button
          type="button"
          @click="deleteBrand"
          form="form"
          class="px-4 py-2 bg-red-500 text-white rounded-md"
      >
        Delete
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
