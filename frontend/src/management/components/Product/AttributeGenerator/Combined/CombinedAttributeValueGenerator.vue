<script setup>
import { reactive, watch } from "vue";
import FocusedTextInput from "@/components/Default/Inputs/FocusedTextInput.vue";

const props = defineProps({
  options: Object,
  category: Object,
  existingAttributes: Array,
});

const emit = defineEmits(["update:combinations"]);

let combinations = reactive(
  props.existingAttributes?.length
    ? props.existingAttributes.map((attr) => ({
        sku: attr.attributes.sku || null,
        quantity: attr.attributes.quantity || null,
        price: attr.attributes.price || null,
        attributes: attr.attributes.attributes.map((attribute) => ({
          id: attribute.id,
          name: attribute.name,
          type: attribute.type,
          value: attribute.value || null,
          attribute_view_type: attribute.attribute_view_type,
        })),
      }))
    : Array.from(
        { length: props.options.combined.numberOfCombinations },
        () => ({
          sku: null,
          quantity: null,
          price: null,
          attributes: props.options.combined.data.map((attribute) => ({
            id: attribute.id,
            name: attribute.name,
            type: attribute.type,
            attribute_view_type: attribute.view_type,
            value: null,
          })),
        }),
      ),
);

const emitUpdate = () => {
  emit("update:combinations", combinations);
};

function clearCombinationsData() {
  combinations = Array.from(
    { length: props.options.combined.numberOfCombinations },
    () => ({
      sku: null,
      quantity: null,
      price: null,
      attributes: props.options.combined.data.map((attribute) => ({
        id: attribute.id,
        name: attribute.name,
        type: attribute.type,
        attribute_view_type: attribute.view_type,
        value: null,
      })),
    }),
  );
}

watch(
  combinations,
  () => {
    emit("update:combinations", combinations);
  },
  { deep: true },
);

watch(
  () => props.options,
  () => {
    clearCombinationsData();
  },
  { deep: true },
);
</script>

<template>
  <div class="space-y-8">
    <div
      v-for="(combination, cIndex) in combinations"
      :key="cIndex"
      class="space-y-4"
    >
      <p>Combination {{ cIndex + 1 }}:</p>
      <div class="ml-8 space-y-4">
        <div class="flex">
          <focused-text-input
            v-model="combination.sku"
            :id="`sku${cIndex}`"
            name="sku"
            label="SKU"
            placeholder="SKU identifier"
            tooltip="Unique identifier of this combination. E.x. phone-red-64gb"
            required
            @input="emitUpdate"
          />
        </div>
        <p>Fill in the attributes:</p>
        <div class="flex items-center gap-x-4 flex-wrap">
          <div
            v-for="(attribute, aIndex) in combination.attributes"
            :key="aIndex"
            class="flex items-center gap-x-4 ml-4"
          >
            <focused-text-input
              v-if="attribute.type.name === 'float'"
              type="number"
              step=".01"
              v-model="attribute.value"
              :id="`values${cIndex + aIndex}`"
              name="value[]"
              placeholder="Your value"
              :label="attribute.name"
              required
              @input="emitUpdate"
            />
            <focused-text-input
              v-else
              :type="attribute.type.name"
              v-model="attribute.value"
              :id="`values${cIndex + aIndex}`"
              name="value[]"
              inputClass="h-14"
              placeholder="Your value"
              :label="attribute.name"
              required
              @input="emitUpdate"
            />
          </div>
        </div>

        <div class="flex gap-4">
          <focused-text-input
            v-model="combination.quantity"
            :id="`quantity${cIndex}`"
            name="quantity[]"
            type="number"
            step="1"
            min="1"
            max="1000000"
            placeholder="1000"
            label="Products quantity"
            required
          />
          <focused-text-input
            v-model="combination.price"
            :id="`price${cIndex}`"
            type="number"
            step=".01"
            min="1"
            max="10000000"
            name="price[]"
            placeholder="100.00"
            label="Price for this combination"
          />
        </div>
      </div>
    </div>
  </div>
</template>
