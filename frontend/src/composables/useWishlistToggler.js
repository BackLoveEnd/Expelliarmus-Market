import {useWishlistStore} from "@/stores/useWishlistStore.js";
import {useToastStore} from "@/stores/useToastStore.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import ToastUnAuthAddToWishlist from "@/components/Default/Toasts/Wishlist/ToastUnAuthAddToWishlist.vue";
import wishlistToastSetting from "@/components/Default/Toasts/Wishlist/toastSettings.js";

export function useWishlistToggler(product) {
    const wishlistStore = useWishlistStore();

    const toastStore = useToastStore();

    const toggleWishlist = async () => {
        if (!product) return;

        const productId = Number(product.id);

        try {
            if (wishlistStore.isProductInWishlist(productId)) {
                await wishlistStore.removeFromWishlist(productId);
            } else {
                await wishlistStore.addToWishlist(product);
            }
        } catch (e) {
            if (e?.status === 401) {
                const content = {component: ToastUnAuthAddToWishlist};
                toastStore.showToast(content, wishlistToastSetting);
            } else {
                toastStore.showToast(e?.response?.data?.message, defaultErrorSettings);
            }
        }
    };

    const isInWishlist = () => {
        return wishlistStore.isProductInWishlist(Number(product.id));
    };

    return {
        toggleWishlist,
        isInWishlist,
    };
}
