<template>
  <main class="space-y-20 first-of-type:mt-20 last-of-type:mb-20">
    <section class="container mx-auto">
      <bread-crumbs :links="links"></bread-crumbs>
    </section>

    <section class="container mx-auto my-20">
      <div class="grid grid-cols-1 md:grid-cols-[auto,1fr] gap-8 mt-20">
        <div
          class="py-10 px-9 rounded-md bg-white drop-shadow-lg max-w-md mx-auto"
        >
          <div class="flex flex-col gap-y-8">
            <div class="space-y-8">
              <div class="flex items-center gap-x-4">
                <div
                  class="w-10 h-10 rounded-full bg-[#db4444] flex justify-center items-center text-white"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
                    />
                  </svg>
                </div>
                <span class="text-base font-medium">Call To Us</span>
              </div>
              <div class="space-y-6">
                <p>We are available 24/7, 7 days a week.</p>
                <p>Phone: +00000000000000</p>
              </div>
            </div>
            <hr class="h-[2px] bg-gray-300 border-0" />
            <div class="space-y-8">
              <div class="flex items-center gap-x-4">
                <div
                  class="w-10 h-10 rounded-full bg-[#db4444] flex justify-center items-center text-white"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="size-6"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
                    />
                  </svg>
                </div>
                <span class="text-base font-medium">Write To Us</span>
              </div>
              <div class="space-y-6">
                <p>
                  Fill out our form and we will contact you within 24 hours.
                </p>
                <p>Emails: customer@expelliarmus.com</p>
                <p>Emails: support@expelliarmus.com</p>
              </div>
            </div>
          </div>
        </div>

        <div class="py-10 px-9 rounded-md bg-white drop-shadow-lg flex-grow">
          <form class="grid gap-y-8">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <base-text-input
                id="name"
                name="name"
                placeholder="Your Name"
              ></base-text-input>
              <base-text-input
                id="email"
                name="email"
                placeholder="Your Email"
                type="email"
              ></base-text-input>
              <div
                class="flex items-center bg-gray-100 rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
              >
                <input
                  type="text"
                  placeholder="Your Phone"
                  class="w-full bg-gray-100 outline-none text-gray-700 placeholder-gray-500 text-base"
                />
              </div>
            </div>
            <div
              class="bg-gray-100 rounded-md px-4 py-4 focus-within:ring-2 focus-within:ring-gray-500"
            >
              <textarea
                class="w-full bg-gray-100 border-none focus:outline-none resize-none"
                rows="10"
                placeholder="Type your text here..."
              ></textarea>
            </div>
            <div class="flex justify-end">
              <button
                type="button"
                class="px-12 py-4 bg-[#db4444] text-white text-center hover:bg-red-900 rounded-md"
              >
                Send Message
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup>
import BreadCrumbs from "@/components/Default/BreadCrumbs.vue";
import BaseTextInput from "@/components/Default/Inputs/BaseTextInput.vue";
import { ref } from "vue";

const links = ref([
  {
    url: "/",
    name: "Home",
  },
  {
    url: "/about-us",
    name: "About",
  },
]);
</script>

<style scoped></style>
