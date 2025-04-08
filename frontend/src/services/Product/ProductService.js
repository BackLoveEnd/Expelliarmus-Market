import api from "@/utils/api.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";

const formatter = useJsonApiFormatter();

export const ProductService = {
    async getProductPreview(id, slug) {
        return await api()
            .get(
                `/management/products/${id}/${slug}`,
            )
            .then((response) => {
                const productAttr = response.data.data.attributes;

                const product = {
                    id: response.data.data.id,
                    title: productAttr.title,
                    article: productAttr.article,
                    main_description: productAttr.main_description,
                    title_description: productAttr.title_description,
                    images: productAttr.images,
                    specifications: productAttr.specifications,
                };

                const includedParsed = formatter.parseIncluded(response.data);

                return {
                    product: product,
                    previewVariations: productAttr.previewVariations,
                    category: includedParsed.category[0],
                    brand: includedParsed.brand[0],
                    variations: includedParsed.variations,
                };
            })
            .catch((e) => {
                throw e;
            });
    },

    async createProduct(productData, relationships) {
        const formatter = useJsonApiFormatter();

        const data = formatter.toJsonApi(
            productData.value,
            "products",
            relationships,
        );

        return await api().post("/management/products/create", data);
    },

    async uploadImagesForProduct(productId, images, previewImage) {
        const formData = new FormData();

        images.forEach((image, index) => {
            formData.append(`images[${index}][image]`, image.file);
            formData.append(`images[${index}][order]`, image.order);
        });

        if (previewImage?.file) {
            formData.append("preview_image", previewImage.file);
        }

        return await api().post(
            "/management/products/" + productId + "/images",
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            },
        );
    },

    async getProductsForEachRootCategory() {
        return await api().get(`/management/categories/products?include=products`);
    },

    async getAttributesForCategory(categoryId) {
        return await api().get(
            "/management/categories/" + categoryId + "/attributes",
        );
    },

    async getProductSpecificationsByCategory(categoryId) {
        return await api().get(
            `/management/product-specifications/category/${categoryId}`,
        );
    },

    async getProductsByCategory(url) {
        return await api().get(url);
    },

    async getProductStaffInfo(productId) {
        return await api()
            .get(
                `/management/products/${productId}/staff?include=warehouse,variations`,
            )
            .then((response) => {
                const productAttr = response.data.data.attributes;

                const product = {
                    id: response.data.data.id,
                    title: productAttr.title,
                    article: productAttr.article,
                    main_description_markdown: productAttr.main_description_markdown,
                    title_description: productAttr.title_description,
                    images: productAttr.images,
                    previewImage: productAttr.preview_image,
                    createdAt: productAttr.created_at,
                    updatedAt: productAttr.updated_at,
                    published: productAttr.published,
                    is_combined_attributes: productAttr.has_combinations,
                    specifications: productAttr.specifications,
                    categoryId: productAttr.category,
                    brandId: productAttr.brand,
                };

                const includedParsed = formatter.parseIncluded(response.data);

                return {
                    product: product,
                    variations: includedParsed.variations,
                    warehouse: includedParsed.warehouse[0].attributes,
                };
            })
            .catch((e) => {
                throw e;
            });
    },

    async editProduct(productData, warehouse, relationships) {
        const formatter = useJsonApiFormatter();

        const data = formatter.toJsonApi(
            {
                title: productData.title,
                product_article: productData.article,
                price: warehouse.price,
                title_description: productData.title_description,
                main_description: productData.main_description_markdown,
                is_combined_attributes: productData.is_combined_attributes,
                total_quantity: warehouse.total_quantity,
            },
            "products",
            relationships,
        );

        return await api().put(`/management/products/${productData.id}`, data);
    },

    async editProductImages(product, images, previewImage) {
        const formData = new FormData();
        images.forEach((image, index) => {
            formData.append(`images[${index}][image]`, image.file ?? "");
            formData.append(`images[${index}][id]`, image.id ?? "");
            formData.append(`images[${index}][image_url]`, image.file ? "" : image.image_url);
            formData.append(`images[${index}][order]`, image.order);
        });

        if (previewImage?.file) {
            formData.append("preview_image", previewImage.file);
        }

        return await api().post(
            "/management/products/" + product.id + "/images/edit",
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            },
        );
    },

    async moveToTrash(productId) {
        return await api().delete(`/management/products/${productId}/trash`);
    },

    async publishProduct(productId) {
        return await api().post(`/management/products/${productId}/publish`);
    },

    async unPublishProduct(productId) {
        return await api().post(`/management/products/${productId}/unpublish`);
    },

    async trashedProducts(params) {
        const url = formatter.formatUrl("/management/products/trashed", params);
        return await api().get(url);
    },

    async restoreProduct(productId) {
        return await api().post(`/management/products/${productId}/restore`);
    },

    async forceDelete(productId) {
        return await api().delete(`/management/products/${productId}`);
    }
};
