import api from "@/utils/api.js";

export const ShopCouponService = {
    async checkForCouponCode(couponCode) {
        return await api().get(`/shop/users/coupons/${couponCode}/check`);
    }
};