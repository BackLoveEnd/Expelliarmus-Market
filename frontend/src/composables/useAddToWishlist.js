import { onUnmounted, ref } from "vue";
import { useAuthStore } from "@/stores/useAuthStore.js";

export function useAddToWishlist(emitter) {
  const isInWishlist = ref(false);

  function addToWishlist() {
    const auth = useAuthStore();

    if (!auth.isAuthenticated) {
      emitter.emit("unauthenticated-add-to-wishlist");
      return;
    }

    isInWishlist.value = !isInWishlist.value;

    isInWishlist.value
      ? emitter.emit("add-to-wishlist")
      : emitter.emit("remove-from-wishlist");
  }

  onUnmounted(() => {
    emitter.off("add-to-wishlist");
    emitter.off("remove-from-wishlist");
  });

  return {
    isInWishlist,
    addToWishlist,
  };
}
