import {useJsonApiFormatter} from '@/composables/useJsonApiFormatter.js';
import api from '@/utils/api.js';

const formatter = useJsonApiFormatter();

export const ShopOrderService = {
    async createOrder(guestUserData = null, couponCode = null) {
        if (guestUserData) {
            const data = formatter.toJsonApi({
                first_name: guestUserData.first_name,
                last_name: guestUserData.last_name,
                email: guestUserData.email,
                phone: guestUserData.country_code + guestUserData.phone_number,
                address: guestUserData.address,
                coupon: couponCode
            }, 'guests');

            return await api().post('/shop/user/orders/checkout', data);
        }

        return await api().post('/shop/user/orders/checkout', formatter.toJsonApi({coupon: couponCode}, 'guests'));
    },

    async getMyOrders(page) {
        return await api().get(`/shop/user/orders?page=${page}`,)
            .then(response => {
                const data = response.data.data.map((order) => ({
                    id: order.id,
                    orderId: order.attributes.order_id,
                    status: order.attributes.status,
                    total: order.attributes.total_price,
                    createdAt: order.attributes.created_at,
                    positions: order.attributes.positions.map((position) => ({
                        id: position.id,
                        productId: position.product_id,
                        productTitle: position.product.title,
                        productImage: position.product.image,
                        quantity: position.quantity,
                        totalPrice: position.total_price,
                        pricePerUnit: position.price_per_unit,
                    })),
                }));

                return {
                    data: data,
                    meta: {
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        total: response.data.meta.total,
                        perPage: response.data.meta.per_page,
                    },
                    links: {
                        first: response.data.links.first,
                        last: response.data.links.last,
                        next: response.data.links.next,
                        prev: response.data.links.prev
                    }
                };
            })
            .catch((e) => {
                throw e;
            });
    },

    async getCancelledOrders(page) {
        return await api().get(`/shop/user/orders/cancelled?page=${page}`,)
            .then(response => {
                const data = response.data.data.map((order) => ({
                    id: order.id,
                    orderId: order.attributes.order_id,
                    status: order.attributes.status,
                    total: order.attributes.total_price,
                    createdAt: order.attributes.created_at,
                    positions: order.attributes.positions.map((position) => ({
                        id: position.id,
                        productId: position.product_id,
                        productTitle: position.product.title,
                        productImage: position.product.image,
                        quantity: position.quantity,
                        totalPrice: position.total_price,
                        pricePerUnit: position.price_per_unit,
                    })),
                }));

                return {
                    data: data,
                    meta: {
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        total: response.data.meta.total,
                        perPage: response.data.meta.per_page,
                    },
                    links: {
                        first: response.data.links.first,
                        last: response.data.links.last,
                        next: response.data.links.next,
                        prev: response.data.links.prev
                    }
                };
            })
            .catch((e) => {
                throw e;
            });
    },

    async cancelOrder(orderId) {
        return await api().delete(`/shop/user/orders/${orderId}`);
    }
};