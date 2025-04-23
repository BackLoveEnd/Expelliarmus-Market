import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import api from "@/utils/api.js";

const formatter = useJsonApiFormatter();

export const ShopOrderService = {
    async createOrder(orderData = null) {
        if (orderData) {
            const data = formatter.toJsonApi({
                first_name: orderData.first_name,
                last_name: orderData.last_name,
                email: orderData.email,
                phone: orderData.country_code + orderData.phone_number,
                address: orderData.address,
                coupon: orderData.coupon ?? null
            }, 'guests');

            return await api().post("/shop/user/order/checkout", data);
        }

        return await api().post("/shop/user/order/checkout");
    }
};