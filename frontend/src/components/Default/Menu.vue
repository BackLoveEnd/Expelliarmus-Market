<template>
  <Menu as="div" class="relative">
    <div class="flex items-center">
      <MenuButton @click="isOpen = !isOpen" v-click-outside="dropDownClose">
        <slot name="menu-button" :isOpen="isOpen"></slot>
      </MenuButton>
    </div>
    <transition
        enter-active-class="transition duration-100 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-75 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
    >
      <MenuItems
          :class="[
          'absolute',
          dropToUp ? 'bottom-full' : 'top-full',
          dropToUp ? 'mb-2' : 'mt-2',
          'right-0 w-56 origin-bottom-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none',
        ]"
      >
        <div class="px-1 py-1">
          <MenuItem
              v-slot="{ active, close }"
              v-for="(link, index) in links"
              :key="index"
              class="menu-item"
          >
            <router-link
                v-show="!link.onlyForSuperManager || auth.isSuperManager"
                :to="link.url"
                @click.capture="menuItemClick(close)"
                :class="[
                active ? 'bg-[#db4444] text-white' : 'text-gray-900',
                'group flex gap-x-4 w-full items-center rounded-md px-2 py-2 text-sm',
              ]"
            >
              <i :class="link.svg"></i>
              {{ link.name }}
            </router-link>
          </MenuItem>
        </div>
        <template v-if="$slots['logout']">
          <div class="px-1 py-1">
            <MenuItem v-slot="{ active }">
              <slot name="logout" :active="active"></slot>
            </MenuItem>
          </div>
        </template>
      </MenuItems>
    </transition>
  </Menu>
</template>

<script setup>
import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {defineProps, ref} from "vue";
import {useScrolling} from "@/composables/useScrolling.js";
import {useAuthStore} from "@/stores/useAuthStore.js";

const isOpen = ref(false);

const auth = useAuthStore();

const props = defineProps({
  links: {
    type: Array,
    required: true,
  },
  dropToUp: {
    type: Boolean,
    default: false,
  },
});

function menuItemClick(close) {
  useScrolling().scrollToTop();
  close();
}

function dropDownClose(event) {
  isOpen.value = false;
}
</script>

<style scoped>
.menu-item:hover {
  img {
    filter: brightness(0) saturate(100%) invert(100%) sepia(0%) saturate(7500%) hue-rotate(118deg) brightness(111%) contrast(106%);
  }
}
</style>
