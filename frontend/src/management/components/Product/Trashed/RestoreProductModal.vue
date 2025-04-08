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

const emit = defineEmits(["close-modal", "restored"]);

const toast = useToastStore();

const closeModal = () => {
  emit("close-modal");
};

const restore = async () => {
  await ProductService.restoreProduct(props.product.id)
      .then((response) => {
        if (response?.status === 200) {
          toast.showToast(response?.data?.message, defaultSuccessSettings);

          emit("restored");

          closeModal();
        }
      })
      .catch((e) => {
        if (e.response?.status === 409) {
          toast.showToast(e.response?.data?.message, defaultErrorSettings);

          closeModal();
        } else {
          toast.showToast("Unknown error. Please, try again or contact us.", defaultErrorSettings);

          closeModal();
        }
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
      <span class="font-semibold">Confirm restoring product?</span>
    </template>
    <template #modalBody>
      <span class="text-sm">Product - {{
          product.title
        }} will be restored from trash and automatically unpublished.</span>
    </template>
    <template #modalFooter>
      <div class="space-x-4">
        <button
            @click="closeModal"
            type="button"
            class="p-2 bg-gray-500 text-white rounded-md hover:bg-gray-700">Cancel
        </button>
        <button @click="restore" type="button" class="p-2 bg-blue-500 text-white rounded-md hover:bg-blue-700">
          Restore
        </button>
      </div>
    </template>
  </default-modal>
</template>

<style scoped>

</style>