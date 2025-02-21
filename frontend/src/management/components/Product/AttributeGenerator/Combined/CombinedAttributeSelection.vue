<script setup>
import CombinedAttributeChooser from "@/management/components/Product/AttributeGenerator/Combined/CombinedAttributeChooser.vue";
import { inject } from "vue";

const props = defineProps({
  attributesForCategory: Array,
  withCombinedAttr: Boolean,
  numberOfCombinations: Number | String | null,
  numberOfAttributes: Number | String | null,
});

const attribute = inject("chosenCombinedAttr");

const attributeUpdateInjector = (data) => {
  attribute.value = {
    data: data,
    numberOfAttributes: props.numberOfAttributes,
    numberOfCombinations: props.numberOfCombinations,
  };
};
</script>

<template>
  <div class="flex flex-col items-center gap-y-4">
    <span>Attributes selection</span>
    <div class="flex flex-col items-center gap-y-2 mb-2">
      <p class="text-sm">
        Now, choose an attributes for your product. If the attribute is not
        presented here, you can type a new one. It will be automatically created
        and linked to the chosen category. Then, choose how the attribute
        selection will look like.
      </p>
      <ol class="text-gray-600 list-decimal list-inside text-xs px-8">
        <li>Checkbox - when the product allows multiple selections.</li>
        <li>Radio button - when the product allows only one selection.</li>
        <li>
          Dropdown - when the product allows only one selection and the number
          of options is considerable.
        </li>
      </ol>
    </div>
    <div class="flex flex-col gap-y-2 mb-4">
      <combined-attribute-chooser
        :attributesForCategory="attributesForCategory"
        :number-of-attributes="numberOfAttributes"
        @update-combo-attributes="attributeUpdateInjector"
      />
    </div>
  </div>
</template>

<style scoped></style>
