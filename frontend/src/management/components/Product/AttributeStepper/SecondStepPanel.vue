<script setup>
import StepPanel from "primevue/steppanel";
import Button from "primevue/button";

defineProps({
  withCombinedAttr: Boolean,
});

const numberOfAttributes = defineModel("numberOfAttributes");

const numberOfCombinations = defineModel("numberOfCombinations");
</script>

<template>
  <StepPanel v-slot="{ activateCallback }" value="2">
    <div class="flex flex-col h-52">
      <div
        class="rounded bg-surface-50 dark:bg-surface-950 max-h-48 flex-auto flex justify-center items-center font-medium"
      >
        <div
          class="w-full overflow-y-auto h-full space-y-4"
          v-if="withCombinedAttr"
        >
          <span class="text-lg font-semibold block text-center">
            Combined attributes
            <span class="text-sm font-normal block mt-2 text-center">
              Please, read the instructions
            </span>
          </span>
          <p class="text-start text-sm font-light">
            For each combination, you will need to define an
            <span class="font-bold">SKU</span> — a short identifier for the
            combination, such as phone-white-128. Depending on the number of
            attributes, fields will be created for input values. For example, in
            the case of a phone, there is a combination of color and storage
            capacity, which means two fields will be created: one for 'color'
            and one for 'storage capacity', along with an additional field for
            the quantity of attributes in this combination, as well as the price
            if it differs from the base price.
          </p>
          <p class="text-start text-sm font-light">
            Below, please specify the
            <span class="font-bold">number of attributes</span> in one
            combination and the
            <span class="font-bold">total number of combinations</span>.
          </p>
          <div class="text-start flex justify-around gap-x-6">
            <div class="flex flex-col">
              <label for="numberOfAttributes" class="text-xs text-gray-700 mb-1"
                >Number of attributes</label
              >
              <input
                id="numberOfAttributes"
                type="number"
                v-model="numberOfAttributes"
                min="2"
                class="px-1 py-0.5 text-xs border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-20"
              />
            </div>
            <div class="flex flex-col">
              <label
                for="numberOfCombinations"
                class="text-xs text-gray-700 mb-1"
                >Number of combinations</label
              >
              <input
                id="numberOfCombinations"
                type="number"
                min="1"
                v-model="numberOfCombinations"
                class="px-1 py-0.5 text-xs border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-20"
              />
            </div>
          </div>
        </div>
        <div class="w-full overflow-y-auto h-full space-y-4" v-else>
          <span class="text-lg font-semibold block text-center">
            Non-Combined attributes</span
          >
          <p class="text-start text-sm font-light">
            This option is suitable for products that have
            <span class="font-bold">only one attribute</span>, such as a
            computer mouse – one model, but with different colors, or alcohol –
            with varying volumes.
          </p>
          <p class="text-start text-sm font-light">
            To proceed, please select the
            <span class="font-bold">number of attributes</span> values for the
            product. For example, a product with 5 different colors.
          </p>
          <div class="flex justify-center !mb-4">
            <div class="flex flex-col">
              <label
                for="oneToManyAttributesQuantity"
                class="text-xs text-gray-700 mb-1"
                >Quantity</label
              >
              <input
                id="oneToManyAttributesQuantity"
                type="number"
                v-model="numberOfAttributes"
                min="1"
                class="px-1 py-0.5 text-xs border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none w-20"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="flex pt-6 justify-between">
      <Button
        label="Back"
        severity="secondary"
        icon="pi pi-arrow-left"
        @click="activateCallback('1')"
      />
      <Button
        label="Next"
        icon="pi pi-arrow-right"
        iconPos="right"
        @click="activateCallback('3')"
      />
    </div>
  </StepPanel>
</template>

<style scoped></style>
