import api from "@/utils/api.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import managerApi from "@/utils/managerApi.js";

const formatter = useJsonApiFormatter();

export const ProductService = {
    async getProductPreview(id, slug) {
        return await managerApi()
            .get(
                `/products/${id}/${slug}`,
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

        return await managerApi().post("/products/create", data);
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

        return await managerApi().post(
            "/products/" + productId + "/images",
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            },
        );
    },

    async getProductsForEachRootCategory() {
        return await managerApi().get(`/categories/products?include=products`);
    },

    async getAttributesForCategory(categoryId) {
        return await managerApi().get(
            "/categories/" + categoryId + "/attributes",
        );
    },

    async getProductSpecificationsByCategory(categoryId) {
        return await managerApi().get(
            `/product-specifications/category/${categoryId}`,
        );
    },

    async getProductsByCategory(url) {
        return await api().get(url);
    },

    async getProductStaffInfo(productId) {
        return await managerApi()
            .get(
                `/products/${productId}/staff?include=warehouse,variations`,
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

        return await managerApi().put(`/products/${productData.id}`, data);
    },

    async editProductImages(product, images, previewImage) {
        const formData = new FormData();
        images.forEach((image, index) => {
            formData.append(`images[${index}][image]`, image.file ?? "");
            formData.append(`images[${index}][id]`, image.id ?? "");
            formData.append(`images[${index}][image_url]`, image.file ? "" : image.image_url ?? "");
            formData.append(`images[${index}][order]`, image.order);
        });

        if (previewImage?.file) {
            formData.append("preview_image", previewImage.file);
        }

        return await managerApi().post(
            "/products/" + product.id + "/images/edit",
            formData,
            {
                headers: {
                    "Content-Type": "multipart/form-data",
                },
            },
        );
    },

    async moveToTrash(productId) {
        return await managerApi().delete(`/products/${productId}/trash`);
    },

    async publishProduct(productId) {
        return await managerApi().post(`/products/${productId}/publish`);
    },

    async unPublishProduct(productId) {
        return await managerApi().post(`/products/${productId}/unpublish`);
    },

    async trashedProducts(params) {
        const url = formatter.formatUrl("/products/trashed", params);
        return await managerApi().get(url);
    },

    async restoreProduct(productId) {
        return await managerApi().post(`/products/${productId}/restore`);
    },

    async forceDelete(productId) {
        return await managerApi().delete(`/products/${productId}`);
    }
};
