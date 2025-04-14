import api from "@/utils/api.js";
import managerApi from "@/utils/managerApi.js";

export const CategoriesShopService = {
    async getCategoriesBrowseList(limit = null) {
        return await api().get(`/shop/home/categories/browse?limit=${limit}`);
    },

    async getChildrenForCategory(categorySlug) {
        return await api().get(`/shop/categories/${categorySlug}/children`);
    },

    async getRootCategories() {
        return await managerApi().get('/categories/root');
    },

    async getOptionAttributesForCategory(categorySlug) {
        return await api().get(`/shop/categories/${categorySlug}/attributes`);
    },

    async getCategoriesForBrand(brandSlug) {
        return await api().get(`/shop/brands/${brandSlug}/categories`);
    }
};