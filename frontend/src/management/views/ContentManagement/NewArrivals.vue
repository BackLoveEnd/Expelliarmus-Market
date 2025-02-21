<script setup>
import { computed, onMounted, ref } from "vue";
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import { ContentManagementService } from "@/services/ContentManagementService";
import { useRouter } from "vue-router";
import { useToastStore } from "@/stores/useToastStore.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import Description from "@/components/Product/Arrival/Description.vue";
import newArrivals from "@/components/Product/Arrival/NewArrivals.vue";




</script>

<template>
<default-container>
  <div class="my-14 space-y-8">
    <section class="container mx-auto">
      <h1 class="font-semibold text-4xl mb-4">New Arrivals Management</h1>
      <div class="flex flex-col items-start mt-12 space-y-8">
        <h2 class="text-xl font-semibold">Arrivals</h2>
        <!--    change card    -->
        <form class="max-w-max mx-4">
          <label for="arrivalsId" class="block mb-2 text-m font-semibold font-medium text-gray-900 dark:text-white"
          >Select an option</label>
          <select id="arrivalsId" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg
          focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
          dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            <option selected>Choose arr-position</option>
            <option value="1">First</option>
            <option value="2">Second</option>
            <option value="3">Third</option>
            <option value="4">Fourth</option>
          </select>
        <!--    picture      -->
          <div>
          <label class="block min-w-max mt-4 mb-2 text-medium font-medium text-gray-900 dark:text-white" for="file_input">Upload file</label>
          <input class="block min-w-max text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" aria-describedby="file_input_help" id="file_input" type="file">
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">SVG, PNG, JPG or GIF (MAX. {{ arrNumberSize }}).</p>

        <!--     title      -->

              <label for="card-title" class="block mt-4 mb-2 text-sm font-medium text-gray-900 dark:text-white">Card title</label>
              <input type="text"
                     id="card-title"
                     class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                     placeholder="Write card title here..."
              >

        <!--    Description      -->

            <label for="card-description" class="block mt-4 mb-2 text-sm font-medium text-gray-900 dark:text-white">Card description</label>
            <textarea id="card-description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                      placeholder="Write card description here..."></textarea>

        <!-- URL -->
            <label for="card-link" class="block mt-4 mb-2 text-sm font-medium text-gray-900 dark:text-white">Product link</label>
            <input type="text" id="card-link"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                   placeholder="Write product URL here... " required />

          </div>
        </form>

        <div class="flex gap-4 mt-4">
          <button
              @click="saveArrivals"
              class="bg-gray-500 text-white px-4 py-2 rounded"
          >
            Save
          </button>
        </div>
      </div>
    </section>
    <hr/>

    <section
        class="container mx-auto space-y-4"

    >
      <h2 class="text-xl font-semibold text-center">Arrivals Preview</h2>
      <new-arrivals/>
    </section>
  </div>
</default-container>
</template>

<style scoped>
button {
  transition: background-color 0.3s;
}

button:hover {
  opacity: 0.9;
}
</style>
