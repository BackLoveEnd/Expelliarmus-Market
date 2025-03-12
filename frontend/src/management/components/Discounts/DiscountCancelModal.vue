<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";

const props = defineProps({
  isModalOpen: Boolean
});

const emit = defineEmits(["modal-closed", "cancel-approved"]);

const cancelDiscount = () => {
  emit("cancel-approved");
};

const closeModal = () => {
  emit("modal-closed");
};
</script>

<template>
  <default-modal :is-active="props.isModalOpen" max-width="max-w-xs">
    <template #modalHeading>
      <span>Are you sure you want to cancel this discount?</span>
    </template>
    <template #modalFooter>
      <div class="space-x-4">
        <button
            type="button"
            @click="closeModal"
            class="py-2 px-4 text-white text-sm bg-gray-500 hover:bg-gray-800 rounded-md"
        >
          No
        </button>
        <button
            type="button"
            @click="cancelDiscount"
            class="py-2 px-4 text-white text-sm bg-red-500 hover:bg-red-800 rounded-md"
        >
          Yes
        </button>
      </div>
    </template>
  </default-modal>
</template>

<style scoped>

</style>