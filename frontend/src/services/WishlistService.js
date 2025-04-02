import api from "@/utils/api.js";

export const WishlistService = {
    async getWishlistForUser(pageNumber) {
        return await api().get(`/user/wishlist?page=${pageNumber ?? 1}`)
            .then((response) => {
                const products = response?.data?.data?.map((product) => ({
                    id: product.attributes.id,
                    title: product.attributes.title,
                    slug: product.attributes.slug,
                    price: product.attributes.price,
                    image: product.attributes.preview_image,
                }));

                return {
                    products: products,
                    nextPage: response?.data?.links?.next_page_number,
                    total: response?.data?.meta?.total
                };
            })
            .catch((e) => {
                throw e;
            });
    },

    async addProductToWishlist(productId) {
        return await api().post(`/user/wishlist/products/${productId}`);
    },

    async removeFromWishlist(productId) {
        return await api().delete(`/user/wishlist/products/${productId}`);
    },

    async clearAllWishlist(productId) {
        return await api().delete(`/user/wishlist/products`);
    }
};