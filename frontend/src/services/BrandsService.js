import api from "@/utils/api";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";

export const BrandsService = {
    async fetchBrands(url) {
        return await api().get(url);
    },

    async createBrand(data) {
        return await api().post(
            "/management/brands",
            useJsonApiFormatter().toJsonApi(data, "brands"),
        );
    },

    async editCategory(data) {
        const formattedData = {
            name: data.brand_name,
            description: data.description,
        };
        return await api().put(
            `/management/brands/${data.id}`,
            useJsonApiFormatter().toJsonApi(formattedData, "brands"),
        );
    },

    async deleteBrand(id) {
        return await api().delete(`/management/brands/${id}`);
    },

    async getProductBrandsByCategory(categoryId) {
        return await api().get(`/shop/products/categories/${categoryId}/brands`)
    }
};
