import api from "@/utils/api.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";

const formatter = useJsonApiFormatter();

export const CartService = {
    async getCartInfo() {
        return await api().get('/shop/user/cart')
            .then((response) => {
                return response?.data?.data?.map((cart) => ({
                    id: cart.id,
                    productId: cart.attributes.product_id,
                    productImage: cart.attributes.product_image,
                    productTitle: cart.attributes.product_title,
                    productSlug: cart.attributes.product_slug,
                    quantity: cart.attributes.quantity,
                    unitPrice: cart.attributes.price_per_unit,
                    finalPrice: cart.attributes.final_price,
                    discount: cart.attributes.discount,
                    variation: cart.attributes.variation
                }));
            })
            .catch((e) => {
                throw e;
            });
    },

    async addToCart(productInfo) {
        const data = formatter.toJsonApi(productInfo, 'cart');

        return await api().post('/shop/user/cart', data);
    },

    async removeFromCart(cartId) {
        return await api().delete(`/shop/user/cart/${cartId}`);
    },

    async clearCart() {
        return await api().delete('/shop/user/cart');
    },

    async updateQuantityForProducts(products) {
        const data = formatter.toJsonApi({
            items: products.map((product) => ({
                cart_id: product.id,
                quantity: product.quantity
            }))
        }, 'cart');

        return await api().patch('/shop/user/cart', data);
    }
};