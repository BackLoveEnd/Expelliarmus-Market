<script setup>
import { viewTypes } from "@/utils/attrubiteViewSelectionType.js";
import SingleAttributeChooser from "@/management/components/Product/AttributeGenerator/Single/SingleAttributeChooser.vue";
import BaseSelectOption from "@/components/Default/Inputs/BaseSelectOption.vue";
import { inject, ref } from "vue";

const props = defineProps({
  attributesForCategory: Array,
  numberOfAttributes: Number | String | null,
});

const attribute = inject("chosenSingleAttr");

const attributeViewType = ref("0");

const attributeUpdateInjector = (data) => {
  attribute.value = {
    ...data,
    attribute_view_type: attributeViewType.value,
    numberOfAttributes: props.numberOfAttributes,
  };
};
</script>

<template>
  <div class="flex flex-col items-center gap-y-4">
    <span>Attribute selection</span>
    <p class="text-sm">
      Now, choose an attribute for your product. If the attribute is not
      presented here, you can type a new one. It will be automatically created
      and linked to the chosen category.
    </p>
    <single-attribute-chooser
      :number-of-attributes="numberOfAttributes"
      :attributesForCategory="attributesForCategory"
      @update-attributes="attributeUpdateInjector"
    />
    <div class="flex flex-col items-center gap-y-2 mb-2">
      <p class="text-sm">
        Then, choose how the attribute selection will look like.
      </p>
      <ol class="text-gray-600 list-decimal list-inside text-xs px-8">
        <li>Checkbox - when the product allows multiple selections.</li>
        <li>Radio button - when the product allows only one selection.</li>
        <li>
          Dropdown - when the product allows only one selection and the number
          of options is considerable.
        </li>
      </ol>
      <div class="w-2/3">
        <base-select-option
          id="viewTypes"
          v-model="attributeViewType"
          :options="viewTypes"
          body-class="shadow-md"
          name="viewTypes"
          placeholder="Select view type"
          required
        />
      </div>
    </div>
  </div>
</template>

<style scoped></style>
