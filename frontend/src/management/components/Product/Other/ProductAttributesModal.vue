<script setup>
import DefaultModal from "@/management/components/Main/DefaultModal.vue";
import {onMounted, provide, ref, watch} from "vue";
import AttributesStepper from "@/management/components/Product/AttributeStepper/AttributesStepper.vue";
import {ProductService} from "@/services/Product/ProductService.js";

const props = defineProps({
  category: Object,
  manuallyGenerated: Boolean,
});

const isModalOpen = ref(false);
const isLastStep = ref(false);
const isGenerated = ref(false);

const attributesForCategory = ref([]);

const chosenSingleAttribute = ref({});
const chosenCombinedAttribute = ref({});

provide("chosenSingleAttr", chosenSingleAttribute);
provide("chosenCombinedAttr", chosenCombinedAttribute);

const generateAttributes = () => {
  emit("update-options", {
    single: chosenSingleAttribute.value,
    combined: chosenCombinedAttribute.value,
  });
  isModalOpen.value = false;
  isGenerated.value = true;
};

function deleteAttributes() {
  chosenSingleAttribute.value = {};
  chosenCombinedAttribute.value = {};

  emit("update-options", {
    single: chosenSingleAttribute.value,
    combined: chosenCombinedAttribute.value,
  });

  emit("deleted-options");

  isGenerated.value = false;
}

onMounted(() => {
  if (props.manuallyGenerated) {
    isGenerated.value = props.manuallyGenerated;
  }
});

watch(isModalOpen, (value) => {
  if (!value) {
    isLastStep.value = false;
  }
});

watch(
    () => props.category.id,
    () => {
      deleteAttributes();
    },
);

const emit = defineEmits(["update-options", "deleted-options"]);

async function openModalAndFetchAttributes() {
  isModalOpen.value = true;

  await ProductService.getAttributesForCategory(props.category.id)
      .then((response) => {
        attributesForCategory.value = response.data.data;
      })
      .catch((e) => {
      });
}
</script>

<template>
  <div class="flex items-center gap-x-4">
    <div v-if="isGenerated === false">
      <span
      >Leave this section, if your product <b>does not have</b> any
        attributes, like color, sizes etc or
      </span>
      <button
          type="button"
          @click="openModalAndFetchAttributes"
          class="px-10 py-3 bg-blue-500 rounded-lg text-white hover:bg-blue-700"
      >
        Generate attribute fields
      </button>
    </div>
    <div class="flex gap-4" v-else>
      <button
          type="button"
          @click="openModalAndFetchAttributes"
          class="px-10 py-3 bg-blue-500 rounded-lg text-white hover:bg-blue-700"
      >
        Re-generate attribute fields
      </button>
      <button
          type="button"
          @click="deleteAttributes"
          class="px-10 py-3 bg-red-500 rounded-lg text-white hover:bg-red-700"
      >
        Delete attributes
      </button>
    </div>
    <default-modal
        :is-active="isModalOpen"
        @close-modal="isModalOpen = false"
        max-width="max-w-2xl"
    >
      <template #modalHeading>
        <span>Attributes generator</span>
      </template>
      <template #modalBody>
        <attributes-stepper
            @last-step="isLastStep = !isLastStep"
            :attributesForCategory="attributesForCategory"
        ></attributes-stepper>
      </template>
      <template #modalFooter>
        <div class="space-x-4">
          <button
              type="button"
              @click="isModalOpen = false"
              class="px-4 py-2 bg-gray-300 text-white rounded hover:bg-gray-400"
          >
            Cancel
          </button>
          <button
              type="button"
              v-show="isLastStep"
              @click="generateAttributes"
              class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700"
          >
            Generate
          </button>
        </div>
      </template>
    </default-modal>
  </div>
</template>

<style scoped></style>
