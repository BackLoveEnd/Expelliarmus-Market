import { defineStore } from "pinia";
import { useToast } from "vue-toastification";

export const useToastStore = defineStore("toast", () => {
  const toast = useToast();

  const showToast = (content, options = {}) => {
    toast(content, options);
  };

  return { showToast };
});
