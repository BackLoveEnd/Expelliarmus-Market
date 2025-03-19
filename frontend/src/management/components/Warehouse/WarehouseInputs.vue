<script setup>
import FocusedTextInput from "@/components/Default/Inputs/FocusedTextInput.vue";

const productArticle = defineModel("productArticle");

const totalQuantity = defineModel("totalQuantity");

const defaultPrice = defineModel("defaultPrice");
</script>

<template>
  <focused-text-input
    tooltip="Please, note that we will add 4 random characters separated by '-', in order to ensure the uniqueness of the article."
    id="product_article"
    name="article"
    v-model="productArticle"
    placeholder="ABC21412"
    label="Product Article (8 or 13 symb.)"
    required
  />
  <focused-text-input
    id="total_quantity"
    placeholder="1000"
    v-model="totalQuantity"
    name="total_quantity"
    label="Total Quantity"
    type="number"
    step="0"
    min="1"
    max="1000000"
    required
  />
  <focused-text-input
    id="default_price"
    placeholder="100.00"
    v-model="defaultPrice"
    tooltip="If your product price depends on attributes, you may leave this field empty."
    name="default_price"
    label="Price (USD)"
    type="number"
    step=".01"
    min="0"
    max="9999999.99"
  />
</template>

<style scoped></style>
