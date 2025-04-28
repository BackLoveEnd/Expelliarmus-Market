import api from "@/utils/api.js";
import managerApi from "@/utils/managerApi.js";

export const ShopCouponService = {
    async checkForCouponCode(couponCode) {
        return await api().get(`/shop/users/coupons/${couponCode}/check`);
    },

    async getCoupons(url) {
        return await managerApi().get(url);
    }
};