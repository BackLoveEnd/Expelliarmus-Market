import {defineStore} from 'pinia';
import {CartService} from "@/services/User/CartService.js";

export const useCartStore = defineStore('cart', {
    state: () => ({
        cartItems: [],
    }),
    getters: {
        totalPrice(state) {
            const total = state.cartItems.reduce((sum, item) => {
                const price = item.discount?.final_price ?? item.unitPrice;
                return sum + price * item.quantity;
            }, 0);

            return total.toFixed(2);
        },
        totalItems(state) {
            return state.cartItems.length;
        },
        isProductInCart: (state) => (productId, variationId = null) => {
            return state.cartItems.some((item) => {
                const sameProduct = Number(productId) === Number(item.productId);

                const sameVariation = variationId === null
                    ? item.variation?.id === null
                    : Number(variationId) === Number(item.variation?.id);

                return sameProduct && sameVariation;
            });
        }
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
                    this.cartItems.push({productId: product.product_id, variation: {id: product.variation_id ?? null}});
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