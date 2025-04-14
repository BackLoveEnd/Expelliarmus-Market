import {defineStore} from "pinia";
import {WishlistService} from "@/services/User/WishlistService.js";

export const useWishlistStore = defineStore('wishlist', {
    state: () => ({
        wishlistItems: [],
        totalItems: 0,
        nextPage: null
    }),
    getters: {
        isProductInWishlist: (state) => (productId) => {
            return state.wishlistItems.some((item) => Number(item.id) === productId);
        }
    },
    actions: {
        async fetchWishlist() {
            await WishlistService.getWishlistForUser()
                .then((response) => {
                    this.wishlistItems = response.products;

                    this.totalItems = response.total;

                    this.nextPage = response.nextPage;
                })
                .catch((e) => {

                });
        },

        async fetchMoreWishlist() {
            if (!this.nextPage) return;

            await WishlistService.getWishlistForUser(this.nextPage)
                .then((response) => {
                    this.wishlistItems.push(...response.products);

                    this.totalItems = response.total;

                    this.nextPage = response.nextPage;
                })
                .catch((e) => {
                });
        },

        async addToWishlist(product) {
            await WishlistService.addProductToWishlist(Number(product.id))
                .then((response) => {
                    ++this.totalItems;

                    this.wishlistItems.push({...product});
                })
                .catch((e) => {
                    if ([400, 409, 401, 429].includes(e?.status)) {
                        throw e;
                    } else {
                        console.error('Unknown error when adding product to wishlist', e);
                    }
                });
        },

        async removeFromWishlist(productId) {
            await WishlistService.removeFromWishlist(productId)
                .then(() => {
                    this.totalItems -= 1;

                    this.wishlistItems = this.wishlistItems.filter((item) => item.id !== productId);
                })
                .catch((e) => {
                    if (e?.status === 409 || e?.status === 429) {
                        throw e;
                    } else {
                        console.error('Unknown error when removing product from wishlist', e);
                    }
                });
        },

        async clearWishlist() {
            return await WishlistService.clearAllWishlist()
                .then((response) => {
                    this.totalItems = 0;

                    this.wishlistItems = [];

                    return response;
                })
                .catch((e) => {
                    console.error('Unknown error when clearing wishlist', e);
                });

        }
    }
});