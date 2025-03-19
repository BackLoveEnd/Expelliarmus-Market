import api from "@/utils/api.js";

export const CategoriesShopService = {
    async getCategoriesBrowseList() {
        return await api().get('/shop/home/categories/browse');
    }
};