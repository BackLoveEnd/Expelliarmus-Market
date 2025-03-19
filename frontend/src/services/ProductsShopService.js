import api from "@/utils/api.js";

export const ProductsShopService = {
    async getFlashSales(limit = 15, offset = 0) {
        return api().get(`/shop/home/products/flash-sales?limit=${limit}&offset=${offset}`);
    },

    async getExploredProducts() {
        return api().get('/shop/home/products/explore');
    }
};