<script setup>
import {Dialog, DialogPanel, TransitionChild, TransitionRoot,} from "@headlessui/vue";

const props = defineProps({
  form: {
    type: String,
    required: true,
  },
  isOpen: {
    type: Boolean,
    default: false,
  },
  submit: {
    type: Function,
    required: true,
  },
});

const emit = defineEmits(["close"]);

function closeModal() {
  emit("close");
}
</script>

<template>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-10">
      <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
      >
        <div class="fixed inset-0 bg-black/25"/>
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div
            class="flex min-h-full items-center justify-center p-4 text-center"
        >
          <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0 scale-95"
              enter-to="opacity-100 scale-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100 scale-100"
              leave-to="opacity-0 scale-95"
          >
            <DialogPanel class="relative p-4 w-full max-w-md h-full md:h-auto">
              <div
                  class="relative p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5"
              >
                <svg
                    class="text-gray-400 dark:text-gray-500 w-11 h-11 mb-3.5 mx-auto"
                    aria-hidden="true"
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    fill="none"
                    viewBox="0 0 24 24"
                >
                  <path
                      stroke="currentColor"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="m16 10 3-3m0 0-3-3m3 3H5v3m3 4-3 3m0 0 3 3m-3-3h14v-3"
                  />
                </svg>
                <p class="mb-4 text-gray-500 dark:text-gray-300">
                  Are you sure you want to change your password?
                </p>
                <div class="flex justify-center items-center space-x-4">
                  <button
                      type="button"
                      @click="closeModal"
                      class="py-2 px-3 text-sm font-medium text-gray-500 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-primary-300 hover:text-gray-900 focus:z-10"
                  >
                    No, cancel
                  </button>
                  <button
                      type="button"
                      @click="submit"
                      class="py-2 px-3 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-600"
                  >
                    Yes, I'm surea
                  </button>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<style scoped></style>
