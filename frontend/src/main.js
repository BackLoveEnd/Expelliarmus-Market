import "./assets/main.css";
import { createApp } from "vue";
import App from "./App.vue";
import router from "@/router.js";
import vClickOutside from "click-outside-vue3";
import mitt from "mitt";
import { createPinia } from "pinia";
import { useAuthStore } from "@/stores/useAuthStore.js";
import Toast from "vue-toastification";
import PrimeVue from "primevue/config";
import Tooltip from "primevue/tooltip";

const pinia = createPinia();

const app = createApp(App);

const emitter = mitt();

app.use(pinia);

app.use(vClickOutside);

app.use(Toast, {
  transition: "Vue-Toastification__fade",
  maxToasts: 3,
  newestOnTop: true,
});

app.directive("tooltip", Tooltip);

app.provide("emitter", emitter);

const authStore = useAuthStore();

authStore.attempt().finally(() => {
  app.use(router);
  app.use(PrimeVue, { theme: "none" });
  app.mount("#app");
});
