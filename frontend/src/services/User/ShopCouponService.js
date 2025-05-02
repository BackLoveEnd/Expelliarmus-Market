import api from '@/utils/api.js'
import managerApi from '@/utils/managerApi.js'
import { useJsonApiFormatter } from '@/composables/useJsonApiFormatter.js'

export const ShopCouponService = {
  async checkForCouponCode (couponCode) {
    return await api().get(`/shop/users/coupons/${couponCode}/check`)
  },

  async getCoupons (url) {
    return await managerApi().get(url)
  },

  async createCoupon (coupon) {
    const data = useJsonApiFormatter().toJsonApi(coupon, 'coupons')

    return await managerApi().post('/users/coupons', data)
  },

  async editCoupon (coupon) {
    const data = useJsonApiFormatter().toJsonApi(coupon, 'coupons')

    return await managerApi().put(`/users/coupons/${coupon.id}`, data)
  },

  async deleteCoupon (couponId) {
    return await managerApi().delete(`/users/coupons/${couponId}`)
  },

  async getMyPersonalCoupon (page = 1) {
    return await api().get(`/shop/user/coupons/personal?page=${page}`)
  },

  async getMyGlobalCoupon (page = 1) {
    return await api().get(`/shop/user/coupons/global?page=${page}`)
  },
}