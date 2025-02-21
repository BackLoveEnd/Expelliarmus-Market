<template>
  <div class="flex flex-wrap gap-4 items-center">
    <button
      @click="$emit('add-subcategory', node)"
      type="button"
      class="text-green-500"
    >
      <i class="fa-solid fa-plus"></i>
    </button>
    <button
      @click="$emit('edit-category', node)"
      type="button"
      class="text-blue-500"
    >
      <i class="fa-solid fa-pen-to-square"></i>
    </button>
    <button
      @click="$emit('delete-category', node)"
      type="button"
      class="text-red-500"
    >
      <i class="fa-solid fa-trash"></i>
    </button>
  </div>
</template>

<script setup>
defineProps({
  node: Object,
});
defineEmits(["add-subcategory", "edit-category", "delete-category"]);
</script>
