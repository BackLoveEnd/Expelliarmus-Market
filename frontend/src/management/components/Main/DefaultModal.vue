<template>
  <Teleport to="body">
    <TransitionRoot
        appear
        :show="isActive"
        as="div"
        class="fixed inset-0 z-50 flex items-center justify-center"
    >
      <Dialog as="div" @close="$emit('close-modal')" class="relative z-10">
        <TransitionChild
            as="div"
            enter="duration-300 ease-out"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100"
            leave-to="opacity-0"
            class="fixed inset-0 bg-black/25"
        />

        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-center justify-center p-4">
            <TransitionChild
                as="div"
                enter="duration-300 ease-out transform"
                enter-from="opacity-0 scale-95"
                enter-to="opacity-100 scale-100"
                leave="duration-200 ease-in transform"
                leave-from="opacity-100 scale-100"
                leave-to="opacity-0 scale-95"
                class="bg-white rounded-xl shadow-xl p-6 w-full max-h-none"
                :class="computedWidth"
            >
              <DialogPanel :class="spaceBetweenTemplates">
                <DialogTitle
                    as="h3"
                    class="text-lg font-medium text-center leading-6 text-gray-900"
                >
                  <slot name="modalHeading"></slot>
                </DialogTitle>

                <div class="mt-4">
                  <slot name="modalBody"></slot>
                </div>

                <slot name="modalFooter"></slot>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </Teleport>
</template>

<script setup>
import {Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot,} from "@headlessui/vue";
import {computed} from "vue";

const props = defineProps({
  isActive: {
    type: Boolean,
    required: true,
  },
  maxWidth: {
    type: String,
    default: "max-w-3xl",
  },
  spaceBetweenTemplates: {
    type: String,
    default: "space-y-4"
  }
});

const emit = defineEmits(["close-modal"]);

const computedWidth = computed(() => {
  return props.maxWidth || "max-w-full";
});
</script>
