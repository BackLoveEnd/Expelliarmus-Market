<template>
  <div v-for="(group, key) in grouped" :key="key">
    <div class="mt-4">
      <div class="feature-title bg-[#f5f5f5] py-2 ps-4 rounded-r-lg">
        <span class="font-semibold text-2xl">{{ group.group }}</span>
      </div>

      <div
        class="spec-item px-6 py-3"
        v-for="(specInGroup, key) in group.specifications"
        :key="key"
      >
        <div class="spec w-full">
          <span class="spec-text pr-2">{{ specInGroup.specification }}</span>
          <span class="dots"></span>
        </div>
        <div class="company pl-2">
          <span
            v-for="(value, index) in specInGroup.value"
            :key="index"
            :class="index === 0 ? 'first-value' : 'additional-value'"
          >
            {{ value }}
          </span>
        </div>
      </div>
    </div>
  </div>
  <div class="flex justify-center">
    <hr class="w-1/2 my-5" />
  </div>
  <div>
    <div
      v-for="(specification, key) in separated.specifications"
      :key="key"
      class="spec-item px-6 py-3"
    >
      <div class="spec w-full">
        <span class="spec-text pr-2">{{ specification.specification }}</span>
        <span class="dots"></span>
      </div>
      <div class="company pl-2">
        <span
          v-for="(value, index) in specification.value"
          :key="index"
          :class="index === 0 ? 'first-value' : 'additional-value'"
          >{{ value }}</span
        >
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  grouped: Array,
  separated: Object,
});
</script>

<style scoped>
.spec-item {
  display: flex;
  align-items: flex-start;
}

.spec {
  font-weight: 500;
  padding-right: 15px;
  display: flex;
  align-items: center;
  position: relative;
}

.spec-text {
  white-space: nowrap;
  background-color: white;
  z-index: 2;
}

.dots {
  flex-grow: 1;
  height: 1px;
  background: repeating-linear-gradient(
    to right,
    currentColor,
    currentColor 1px,
    transparent 2px,
    transparent 4px
  );
  position: absolute;
  left: 0;
  right: 0;
  top: 50%;
  z-index: 1;
}

.company {
  display: flex;
  flex-direction: column;
  align-items: end;
}

.first-value {
  white-space: nowrap;
}

.additional-value {
  display: block;
}
</style>
