import {defineStore} from 'pinia';
import {CartService} from "@/services/CartService.js";

export const useCartStore = defineStore('cart', {
    state: () => ({
        cartItems: [],
        totalItemsCount: 0
    }),
    getters: {
        totalPrice(state) {
            return state.cartItems.reduce((sum, item) => sum + item.attributes.final_price, 0);
        },
        totalItems(state) {
            return state.totalItemsCount === 0 ? state.cartItems.length : state.totalItemsCount + state.cartItems.length;
        },
        isProductInCart: (state) => (productId) => {
            return state.cartItems.some((item) => Number(productId) === Number(item.productId));
        },
    },
    actions: {
        async fetchCart() {
            await CartService.getCartInfo()
                .then((response) => {
                    this.cartItems = response;
                })
                .catch((e) => {
                    throw e;
                });
        },

        async addToCart(product) {
            await CartService.addToCart(product)
                .then(() => {
                    this.totalItemsCount += 1;
                })
                .catch((e) => {
                    if ([422, 400].includes(e.status)) {
                        throw e;
                    } else {
                        console.error('Error adding product to cart', e);
                    }
                });
        },

        removeFromCart(productId) {

        },

        updateQuantity(productId, quantity) {

        },

        clearCart() {

        }
    }
});
