<script setup>
import {reactive} from "vue";
import Tag from "primevue/tag";
import MultiSelect from "primevue/multiselect";

let products = reactive([]);

</script>

<template>
  <section class="container mx-auto h-full">
    <div :class="{
          'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 place-items-center': products.length > 0,
          'grid grid-cols-1 sm:grid-cols-2 gap-4 place-items-center': products.length === 0
      }">
      <div class="flex flex-col justify-around h-full">
        <div class="flex gap-y-2 flex-col">
          <div class="text-lg font-semibold text-center">Total: {{ total }} products</div>
          <div class="text-sm text-center">Showing {{ products.length }} products</div>
        </div>
        <div class="flex flex-col gap-y-4">
          <label class="text-center font-semibold">Filters & Sorting</label>

          <MultiSelect
              @update:modelValue=""
              optionLabel="name"
              filter
              placeholder="Select Sort Fields"
              display="chip"
              class="w-full md:w-80"
          >
            <template #option="{ option }">
              <div class="flex items-center justify-between w-full">
                <span>{{ option.name }}</span>
                <div class="flex gap-1 ml-2">
                  <button
                      class="px-2 py-1 rounded text-xs"
                  >
                    ASC
                  </button>
                  <button
                      class="px-2 py-1 rounded text-xs"
                  >
                    DESC
                  </button>
                </div>
              </div>
            </template>
          </MultiSelect>
        </div>
      </div>

      <template v-if="products.length > 0">
        <div v-for="(product, index) in products" :key="product.id" class="max-w-[240px] my-8">
          <div class="bg-transparent rounded-lg overflow-hidden shadow-md">
            <div class="p-5 pb-0 overflow-hidden">
              <img :src="product.preview_image" alt="Product Image" class="w-full h-full object-cover rounded-lg"/>
            </div>
            <div class="p-5 space-y-2">
              <div class="text-sm text-center text-gray-900">
                {{ product.title }}
              </div>
              <div class="flex flex-col items-start mt-2 space-y-1">
                <span class="text-xs text-gray-500">Article: {{ product.article }}</span>
                <span class="text-xs text-gray-500">Quantity: {{ product.quantity }}</span>
                <span class="text-xs text-gray-500">Deleted At: {{ product.deleted_at }}</span>
                <span class="text-xs text-gray-500">
                Status:
                <Tag class="text-xs" :value="product.status.name" :severity="getSeverity(product.status.name)"/>
              </span>
              </div>
              <div class="flex justify-between">
                <button
                    type="button"
                    class="bg-gray-200 text-xs rounded-md p-1 hover:bg-gray-400"
                >
                  Delete Forever
                </button>
                <button
                    type="button"
                    class="bg-green-200 text-xs rounded-md p-1 hover:bg-green-400"
                >
                  Restore
                </button>
              </div>
            </div>
          </div>
        </div>
        <!--        <div v-if="products.length < total">
                  <button type="button" class="flex flex-col items-center gap-y-2" @click="loadMore">
                    <i class="pi pi-sync text-4xl text-[#374151]" :class="{ 'pi-spin': isLoading }"></i>
                    <span class="font-semibold text-[#374151]">Load more</span>
                  </button>
                </div>
                <div v-else class="flex flex-col items-center gap-y-2">
                  <i class="pi pi-check text-green-500 text-3xl"></i>
                  <span class="text-[#374151] text-sm font-semibold">That all.</span>
                </div>-->
      </template>
      <div
          v-else
          class="no-file-found flex flex-col items-center justify-center py-8 px-4 text-center rounded-lg">
        <svg class="w-12 h-12 dark:text-gray-400 text-gray-700" stroke="currentColor" fill="currentColor"
             stroke-width="0" viewBox="0 0 24 24" height="200px" width="200px" xmlns="http://www.w3.org/2000/svg">
          <g id="File_Off">
            <g>
              <path
                  d="M4,3.308a.5.5,0,0,0-.7.71l.76.76v14.67a2.5,2.5,0,0,0,2.5,2.5H17.44a2.476,2.476,0,0,0,2.28-1.51l.28.28c.45.45,1.16-.26.7-.71Zm14.92,16.33a1.492,1.492,0,0,1-1.48,1.31H6.56a1.5,1.5,0,0,1-1.5-1.5V5.778Z"></path>
              <path
                  d="M13.38,3.088v2.92a2.5,2.5,0,0,0,2.5,2.5h3.07l-.01,6.7a.5.5,0,0,0,1,0V8.538a2.057,2.057,0,0,0-.75-1.47c-1.3-1.26-2.59-2.53-3.89-3.8a3.924,3.924,0,0,0-1.41-1.13,6.523,6.523,0,0,0-1.71-.06H6.81a.5.5,0,0,0,0,1Zm4.83,4.42H15.88a1.5,1.5,0,0,1-1.5-1.5V3.768Z"></path>
            </g>
          </g>
        </svg>
        <h3 class="text-xl font-medium mt-4 text-gray-700 dark:text-gray-200">Products not found.</h3>
        <p class="text-gray-500 dark:text-gray-400 mt-2">
          Products on discount you are looking for not found.
        </p>
      </div>
    </div>
  </section>
</template>

<style>
.p-select {
  @apply bg-white
}

.p-multiselect-label.p-placeholder {
  @apply text-[#374151] font-medium;
}
</style>
