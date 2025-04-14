<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {ProductService} from "@/services/Product/ProductService.js";
import {useToastStore} from "@/stores/useToastStore.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";

const props = defineProps({
  isOpen: Boolean,
  product: Object
});

const emit = defineEmits(["close-modal", "deleted"]);

const toast = useToastStore();

const closeModal = () => {
  emit("close-modal");
};

const forceDelete = async () => {
  await ProductService.forceDelete(props.product.id)
      .then((response) => {
        if (response?.status === 200) {
          toast.showToast(response?.data?.message, defaultSuccessSettings);

          emit("deleted");

          closeModal();
        }
      })
      .catch((e) => {
        if (e.response?.status === 409) {
          toast.showToast(e.response?.data?.message, defaultErrorSettings);

          closeModal();
        } else if (e.response?.status > 500) {
          toast.showToast("Unknown error. Please, try again or contact us.", defaultErrorSettings);
        }

        closeModal();
      });
};
</script>

<template>
  <default-modal
      :is-active="isOpen"
      @close-modal="closeModal"
      max-width="max-w-sm"
      space-between-templates="space-y-4"
  >
    <template #modalHeading>
      <div class="flex gap-x-2 items-center">
        <i class="pi pi-exclamation-triangle text-red-500 text-2xl"></i>
        <span class="font-semibold">Confirm force deleting product?</span>
      </div>
    </template>
    <template #modalBody>
      <span class="text-sm">Product - {{
          product.title
        }} will be <span class="font-bold">permanently</span> deleted. Are you sure to perform this action?</span>
    </template>
    <template #modalFooter>
      <div class="space-x-4">
        <button
            @click="closeModal"
            type="button"
            class="p-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel
        </button>
        <button @click="forceDelete" type="button" class="p-2 bg-red-500 text-white rounded-md hover:bg-red-700">
          Delete
        </button>
      </div>
    </template>
  </default-modal>
</template>

<style scoped>

</style>