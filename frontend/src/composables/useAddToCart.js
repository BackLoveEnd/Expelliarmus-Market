import {inject, onUnmounted, ref} from "vue";

export function useAddToCart() {
    const emitter = inject("emitter");

    const isInCart = ref(false);

    function addToCart() {
        isInCart.value = !isInCart.value;

        isInCart.value
            ? emitter.emit("add-to-cart")
            : emitter.emit("remove-from-cart");
    }

    onUnmounted(() => {
        emitter.off("add-to-cart");
        emitter.off("remove-from-cart");
    });

    return {
        isInCart,
        addToCart,
    };
}