import api from "@/utils/api.js";
import { useJsonApiFormatter } from "@/composables/useJsonApiFormatter.js";

export const CategoryService = {
  async getCategoriesTree() {
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

    return await api().post("/management/categories", data);
  },

  async editCategory(payload, attributes) {
    const data = useJsonApiFormatter().toJsonApi(
      {
        name: payload.name,
      },
      "categories",
      { attributes: attributes },
    );

    return await api().put(`/management/categories/${payload.id}`, data);
  },

  async deleteCategory(id) {
    return await api().delete(`/management/categories/${id}`);
  },

  async getAttributesForCategory(id) {
    return await api().get(`/management/categories/${id}/attributes`);
  },

  async deleteCategoryAttribute(categoryId, attributeId) {
    return await api().delete(
      `/management/categories/${categoryId}/attributes/${attributeId}`,
    );
  },

  async uploadCategoryIcon(file, categoryId) {
    const formatData = new FormData();

    formatData.append("icon", file);

    return await api().post(
      `/management/categories/${categoryId}/icon`,
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

    return await api().post(
      `/management/categories/${categoryId}/icon`,
      formatData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      },
    );
  },
};
