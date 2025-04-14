import api from "@/utils/api.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import managerApi from "@/utils/managerApi.js";

export const BrandsService = {
    async fetchBrands(url) {
        return await api().get(url);
    },

    async createBrand(data) {
        return await managerApi().post(
            "/brands",
            useJsonApiFormatter().toJsonApi(data, "brands"),
        );
    },

    async editCategory(data) {
        const formattedData = {
            name: data.brand_name,
            description: data.description,
        };
        return await managerApi().put(
            `/brands/${data.id}`,
            useJsonApiFormatter().toJsonApi(formattedData, "brands"),
        );
    },

    async getBrandInfo(id) {
        return await api().get(`/shop/brands/${id}`);
    },

    async uploadImage(image, brandId) {
        const data = new FormData();

        data.append('image', image);

        return await managerApi().post(`/brands/logo/${brandId}`, data, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });
    },

    async deleteBrand(id) {
        return await managerApi().delete(`/brands/${id}`);
    },

    async getProductBrandsByCategory(categoryId) {
        return await api().get(`/shop/products/categories/${categoryId}/brands`);
    }
};
