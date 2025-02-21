<template>
  <p class="text-xl">
    Attribute -
    <span class="text-semibold">{{ attributesData.attribute_name }}</span>
  </p>

  <div
    class="flex flex-col gap-y-4 mt-4"
    v-for="(attr, index) in attributesData.attributes"
    :key="index"
  >
    <div class="flex gap-x-4 items-center">
      <focused-text-input
        v-if="attributeTypeName === 'float'"
        id="value"
        name="value[]"
        type="number"
        step=".01"
        placeholder="Your value"
        :label="`Value ${index + 1}`"
        v-model="attr.value"
        @input="emitUpdate"
        required
      />
      <focused-text-input
        v-else
        id="value"
        name="value[]"
        :type="attributeTypeName"
        placeholder="Your value"
        inputClass="h-14"
        :label="`Value ${index + 1}`"
        v-model="attr.value"
        @input="emitUpdate"
        required
      />
      <focused-text-input
        id="quantity"
        name="quantity[]"
        type="number"
        step="1"
        min="1"
        max="1000000"
        placeholder="1000"
        label="Quantity"
        v-model.number="attr.quantity"
        @input="emitUpdate"
        required
      />
      <focused-text-input
        id="price"
        type="number"
        step=".01"
        min="1"
        max="10000000"
        name="price[]"
        placeholder="100.00"
        label="Price"
        v-model="attr.price"
        @input="emitUpdate"
      />
    </div>
  </div>
</template>

<script setup>
import {reactive, ref, watch} from "vue";
import FocusedTextInput from "@/components/Default/Inputs/FocusedTextInput.vue";

const props = defineProps({
  options: Object,
  category: Object,
  existingAttributes: Array,
});

const emit = defineEmits(["update:attributesData"]);

let attributesData = reactive(
  props.existingAttributes?.length
    ? {
        attribute_id: props.existingAttributes[0].attributes.attribute_id,
        attribute_name: props.existingAttributes[0].attributes.attribute_name,
        attribute_type: props.existingAttributes[0].attributes.attribute_type_id,
        attribute_view_type: props.existingAttributes[0].attributes.attribute_view_type,
        attributes: props.existingAttributes.map((attr) => ({
          value: attr.attributes.value,
          quantity: attr.attributes.quantity,
          price: attr.attributes.price,
        })),
      }
    : {
        attribute_id: props.options?.single?.attribute_id || null,
        attribute_name: props.options?.single?.attribute_name || "",
        attribute_type: props.options?.single?.attribute_type?.id,
        attribute_view_type: props.options?.single?.attribute_view_type || 0,
        attributes: Array.from(
          { length: props.options?.single?.numberOfAttributes || 0 },
          () => ({
            value: null,
            quantity: null,
            price: null,
          }),
        ),
      },
);

let attributeTypeName = ref(
    props.existingAttributes?.length
    ? props.existingAttributes[0].attributes.attribute_type : props.options?.single?.attribute_type?.name
);

const emitUpdate = () => {
  emit("update:attributesData", attributesData);
};

function clearAttributesData() {
  attributesData = {
    attribute_id: props.options?.single?.attribute_id || null,
    attribute_name: props.options?.single?.attribute_name || "",
    attribute_type: props.options?.single?.attribute_type?.id,
    attribute_view_type: props.options?.single?.attribute_view_type || 0,
    attributes: Array.from(
      { length: props.options?.single?.numberOfAttributes || 0 },
      () => ({
        value: null,
        quantity: null,
        price: null,
      }),
    ),
  };

  attributeTypeName.value = props.options?.single?.attribute_type?.name;
}

watch(
  attributesData,
  () => {
    emitUpdate();
  },
  { deep: true },
);

watch(
  () => props.options,
  () => {
    clearAttributesData();
  },
  { deep: true },
);
</script>
