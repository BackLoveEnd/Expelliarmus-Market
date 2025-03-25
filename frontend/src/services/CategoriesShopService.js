import api from "@/utils/api.js";

export const CategoriesShopService = {
    async getCategoriesBrowseList(limit = null) {
        return await api().get(`/shop/home/categories/browse?limit=${limit}`);
    },

    async getChildrenForCategory(categorySlug) {
        return await api().get(`/shop/categories/${categorySlug}/children`);
    },

    async getRootCategories() {
        return await api().get('/management/categories/root');
    }
};