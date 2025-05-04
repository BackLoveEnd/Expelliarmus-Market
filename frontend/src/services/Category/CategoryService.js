import api from "@/utils/api.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import managerApi from "@/utils/managerApi.js";

export const CategoryService = {
    async getCategoriesTree(limit = null) {
        if (limit) {
            return await api().get(`/management/categories?limit=${limit}`);
        }

        return await api().get("/management/categories");
    },

    async createCategory(payload, attributes) {
        const data = useJsonApiFormatter().toJsonApi(
            {
                name: payload.name,
                parent: payload.parent ?? null,
            },
            "categories",
            {
                attributes: attributes,
            },
        );

        return await managerApi().post("/categories", data);
    },

    async editCategory(payload, attributes) {
        const data = useJsonApiFormatter().toJsonApi(
            {
                name: payload.name,
            },
            "categories",
            {attributes: attributes},
        );

        return await managerApi().put(`/categories/${payload.id}`, data);
    },

    async deleteCategory(id) {
        return await managerApi().delete(`/categories/${id}`);
    },

    async getAttributesForCategory(id) {
        return await managerApi().get(`/categories/${id}/attributes`);
    },

    async deleteCategoryAttribute(categoryId, attributeId) {
        return await managerApi().delete(
            `/categories/${categoryId}/attributes/${attributeId}`,
        );
    },

    async uploadCategoryIcon(file, categoryId) {
        const formatData = new FormData();

        formatData.append("icon", file);

        return await managerApi().post(
            `/categories/${categoryId}/icon`,
            formatData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            },
        );
    },

    async editCategoryIcon(file, categoryId) {
        const formatData = new FormData();

        formatData.append("icon", file);

        return await managerApi().post(
            `/categories/${categoryId}/icon`,
            formatData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            },
        );
    },
};
