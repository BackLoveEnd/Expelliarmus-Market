import {defineStore} from 'pinia';
import {CartService} from "@/services/User/CartService.js";

export const useCartStore = defineStore('cart', {
    state: () => ({
        cartItems: [],
    }),
    getters: {
        totalPrice(state) {
            return state.cartItems.reduce((sum, item) => sum + (item.unitPrice * item.quantity), 0);
        },
        totalItems(state) {
            return state.cartItems.length;
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
                .then((response) => {
                    this.cartItems.length += 1;
                })
                .catch((e) => {
                    if ([422, 400].includes(e.status)) {
                        throw e;
                    } else {
                        console.error('Error adding product to cart', e);
                    }
                });
        },

        async removeFromCart(cartId) {
            await CartService.removeFromCart(cartId)
                .then(() => {
                    this.cartItems = this.cartItems.filter((item) => item.id !== cartId);
                })
                .catch((e) => {
                    console.error('Error removing from cart', e);
                });
        },

        async updateQuantity(products) {
            await CartService.updateQuantityForProducts(products)
                .catch((e) => {
                    if ([422, 400].includes(e.status)) {
                        throw e;
                    } else {
                        console.error('Failed to update products', e);
                    }
                });
        },

        async clearCart() {
            return await CartService.clearCart()
                .then(() => {
                    this.cartItems = [];
                });
        }
    }
});