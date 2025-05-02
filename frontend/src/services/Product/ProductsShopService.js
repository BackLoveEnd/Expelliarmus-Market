import api from "@/utils/api.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";

const formatter = useJsonApiFormatter();
export const ProductsShopService = {
    async getFlashSales(limit = 15, offset = 0) {
        return api().get(`/shop/home/products/flash-sales?limit=${limit}&offset=${offset}`);
    },

    async getExploredProducts() {
        return api().get('/shop/home/products/explore');
    },

    async getSuggestions() {
        return await api().get('/shop/products/suggestions');
    },

    async getTopSellers() {
        return await api().get('/shop/products/top-sellers');
    },

    async getRelatedProduct(categorySlug) {
        return api().get(`/shop/categories/${categorySlug}/products`);
    },

    async getProductsShopCard(params) {
        const link = formatter.formatUrl(`/shop/products`, params);

        return api().get(link);
    },

    async getMinMaxPrices() {
        return api().get('/shop/products/staff/prices-range');
    },

    async getMinMaxPricesForCategory(categorySlug) {
        return api().get(`/shop/products/categories/${categorySlug}/staff/prices-range`);
    },

    async getMinMaxPricesForBrand(brandSlug) {
        return api().get(`/shop/products/brands/${brandSlug}/staff/prices-range`);
    },

    async getProduct(productId, productSlug) {
        return await api()
            .get(
                `/shop/products/${productId}/${productSlug}`,
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
                    discount: productAttr.discount ?? null
                };

                if (productAttr.discount) {
                    product.discount = productAttr.discount;
                }

                const includedParsed = formatter.parseIncluded(response.data);

                return {
                    product: product,
                    previewVariations: productAttr.previewVariations,
                    category: includedParsed.category[0],
                    brand: includedParsed.brand[0],
                    warehouse: includedParsed.warehouse[0],
                    variations: includedParsed.variations,
                };
            })
            .catch((e) => {
                throw e;
            });
    }
};