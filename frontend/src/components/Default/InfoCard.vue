<template>
  <div
    class="card w-72 h-60 border border-[#b3b3b3] rounded-md flex flex-col justify-center items-center space-y-6 hover:border-0 transition"
  >
    <div
      class="first-circle w-20 h-20 rounded-full bg-[#c1c1c1] flex justify-center items-center"
    >
      <div
        class="second-circle w-14 h-14 rounded-full bg-black flex justify-center items-center text-white"
      >
        <slot></slot>
      </div>
    </div>
    <div class="text-center space-x-3">
      <span class="text-3xl font-bold">{{ title }}</span>
      <p>{{ desc }}</p>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from "vue";

const props = defineProps({
  title: String,
  desc: String,
});
</script>

<style scoped>
.card {
  transition:
    background-color 0.1s ease-out 0.1s,
    color 0.1s ease-out 0.1s,
    border 0.1s ease-out 0.1s,
    box-shadow 0.1s ease-out 0.1s;
}

.card:hover {
  background-color: #db4444;
  color: white;
  -webkit-box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.75);
  -moz-box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.75);
  box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.75);
}

.first-circle {
  transition: background-color 0.1s ease-out 0.1s;
}

.card:hover .first-circle {
  background-color: #e67c7c;
}

.second-circle {
  transition: background-color 0.1s ease-out 0.1s;
}

.card:hover .second-circle {
  background-color: white;
}
</style>
