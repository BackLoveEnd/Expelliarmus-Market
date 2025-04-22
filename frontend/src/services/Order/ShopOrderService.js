import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import api from "@/utils/api.js";

const formatter = useJsonApiFormatter();

export const ShopOrderService = {
    async createOrder(guestUserData = null) {
        if (guestUserData) {
            const data = formatter.toJsonApi({
                first_name: guestUserData.first_name,
                last_name: guestUserData.last_name,
                email: guestUserData.email,
                phone: guestUserData.country_code + guestUserData.phone_number,
                address: guestUserData.address,
            }, 'guests');

            return await api().post("/shop/user/order/checkout", data);
        }

        return await api().post("/shop/user/order/checkout");
    }
};