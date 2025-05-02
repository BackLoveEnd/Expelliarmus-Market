<script setup>
import ProductPhotoTabs from "@/shop/views/Product/ProductPhotoTabs.vue";
import ProductCartModal from "@/components/Product/Cart/ProductCartModal.vue";
import PurchaseButton from "@/components/Product/Main/PurchaseButton.vue";
import Description from "@/components/Product/Main/Description.vue";
import QuantityAdjuster from "@/components/Product/Main/QuantityAdjuster.vue";
import {computed, onBeforeUnmount, reactive, ref, watch} from "vue";
import {ProductsShopService} from "@/services/Product/ProductsShopService.js";
import SectionTitle from "@/components/Default/SectionTitle.vue";
import Specs from "@/components/Product/Main/Specs.vue";
import SingleVariationsViewer from "@/components/Product/Main/SingleVariationsViewer.vue";
import CombinedVariationsViewer from "@/components/Product/Main/CombinedVariationsViewer.vue";
import {useBreadCrumbStore} from "@/stores/useBreadCrumbStore.js";
import BreadCrumbs from "@/components/Default/BreadCrumbs.vue";
import {useRouter} from "vue-router";
import {useWishlistToggler} from "@/composables/useWishlistToggler.js";

const props = defineProps({
  productId: Number | String,
  productSlug: String
});

const productInfo = reactive({
  product: {
    id: null,
    title: null,
    article: null,
    main_description: null,
    title_description: null,
    images: null,
    specifications: null,
    discount: null,
  },
  warehouse: {
    price: null,
    quantity: null,
    status: null
  },
  brand: {
    brand_name: null,
    slug: null
  },
  category: {
    name: null,
    slug: null
  },
  previewVariations: [],
  variations: []
});

const router = useRouter();

const breadcrumbStore = useBreadCrumbStore();

const wishlist = useWishlistToggler({
  title: 'Title',
  id: props.productId,
  slug: props.productSlug
});

const isCartModalOpen = ref(false);

const quantity = ref(1);

const price = ref(0);

const pricePerUnit = computed(() => price.value.toFixed(2));

let selectedVariation = ref({
  id: null,
  price: null,
  discount: null,
  availability: null
});

const emit = defineEmits(["product-data"]);

function toggleCartModal() {
  isCartModalOpen.value = !isCartModalOpen.value;
}

const priceDependOnQuantity = computed(() =>
    (price.value * quantity.value).toFixed(2),
);

const cartInfoSummarize = computed(() => {
  return {
    product_id: productInfo.product.id,
    variation_id: selectedVariation.value.id ?? null,
    quantity: quantity.value
  };
});

const imagesUrls = computed(() => {
  return productInfo.product?.images?.map((image) => image.image_url);
});

const discountIfAvailable = computed(() => {
  if (productInfo?.product?.discount !== null) {
    return {
      old_price: productInfo?.product?.discount.old_price,
      percentage: productInfo?.product?.discount.percentage,
      end_at: productInfo?.product?.discount.end_at
    };
  }

  if (selectedVariation.value.discount === null) {
    return null;
  }

  return {
    old_price: selectedVariation.value.discount.old_price,
    percentage: selectedVariation.value.discount.percentage,
    end_at: selectedVariation.value.discount.end_at
  };
});

const handleSelectedVariation = (variation) => {
  setSelectedVariation(variation);

  if (selectedVariation.value.discount !== null) {
    price.value = parseFloat(selectedVariation.value.discount.discount_price);
  } else {
    price.value = parseFloat(variation.attributes.price);
  }
};

async function getProduct() {
  await ProductsShopService.getProduct(props.productId, props.productSlug)
      .then((response) => {
        Object.assign(productInfo, response);

        breadcrumbStore.setBreadcrumbs([
          {name: "Home", url: "/"},
          {
            name: productInfo?.category?.attributes?.name,
            url: `/shop/categories/${productInfo?.category?.attributes?.slug}`
          },
          {name: productInfo.brand?.attributes?.brand_name, url: "#"},
          {name: productInfo.product?.title, url: "#"},
        ]);

        if (!isNaN(parseFloat(productInfo?.warehouse?.attributes.price))) {
          price.value = productInfo?.warehouse?.attributes.price;
        }

        emit("product-data", {
          product: productInfo?.product,
          category: productInfo?.category?.attributes,
          brand: productInfo?.brand?.attributes
        });
      })
      .catch((e) => {
        if (e?.status === 404) {
          router.push({name: "not-found"});
        }
      });
}

watch(
    () => [props.productId, props.productSlug],
    async ([newProductId, newProductSlug], [oldProductId, oldProductSlug]) => {
      if (newProductId !== oldProductId || newProductSlug !== oldProductSlug) {
        await getProduct();
      }
    },
);

function setSelectedVariation(variation) {
  selectedVariation.value.price = variation.attributes.price;
  selectedVariation.value.id = variation.attributes.id;
  selectedVariation.value.discount = variation.attributes.discount;
  selectedVariation.value.availability = variation.attributes.availability;
}

await getProduct();

onBeforeUnmount(() => breadcrumbStore.clearBreadcrumbs());
</script>

<template>
  <div class="space-y-20 mt-5">
    <section class="container mx-auto max-w-screen-2xl">
      <bread-crumbs :links="breadcrumbStore.breadcrumbs"></bread-crumbs>
    </section>
    <section class="container mx-auto">
      <div class="flex justify-between">
        <product-photo-tabs :images="imagesUrls"/>
        <div class="flex flex-col  gap-y-8 items-start min-w-[35%]">
          <div class="flex flex-col gap-y-4 w-full" v-if="productInfo.product?.id">
            <description
                :price="priceDependOnQuantity"
                :price-per-unit="pricePerUnit"
                :discount="discountIfAvailable"
                :title="productInfo.product?.title"
                :title-desc="productInfo.product?.title_description"
                :article="productInfo.product?.article"
                :status="selectedVariation?.availability ?? productInfo?.warehouse?.status"
            >
            </description>
          </div>
          <div class="flex flex-col gap-y-8" v-if="productInfo.previewVariations">
            <combined-variations-viewer
                v-if="Array.isArray(productInfo?.previewVariations)"
                :previewed-variations="productInfo?.previewVariations"
                :variations="productInfo?.variations"
                @selected-option="handleSelectedVariation"
            />
            <single-variations-viewer
                v-else
                :previewed-variation="productInfo?.previewVariations"
                :variations="productInfo?.variations"
                @selected-option="handleSelectedVariation"
            />
          </div>
          <div class="flex items-center gap-x-8">
            <quantity-adjuster v-model="quantity"/>
            <div>
              <purchase-button
                  :product-info="cartInfoSummarize"
                  @open-cart-modal="toggleCartModal"
              ></purchase-button>
              <product-cart-modal
                  :is-open="isCartModalOpen"
                  @close-cart-modal="toggleCartModal"
              />
            </div>
            <div>
              <button
                  @click="wishlist.toggleWishlist()"
                  class="px-3 py-2 text-white text-center hover:bg-gray-200 border border-gray-700 rounded-md"
              >
                <i
                    class="pi text-gray-800 text-xl"
                    :class="wishlist.isInWishlist() ? 'pi-heart-fill text-red-500' : 'pi-heart'">
                </i>
              </button>
            </div>
          </div>
          <div class="rounded-md w-full flex gap-x-4">
            <div
                class="flex justify-start items-center border border-gray-700 py-4 px-8 rounded-md"
            >
              <div class="flex gap-x-6 items-center">
                <svg
                    viewBox="0 0 24 24"
                    fill="black"
                    xmlns="http://www.w3.org/2000/svg"
                    width="40"
                    height="40"
                    color="black"
                >
                  <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M4.25 3.25C3.00736 3.25 2 4.25736 2 5.5V16C2 17.2426 3.00736 18.25 4.25 18.25H4.30197C4.56712 19.6729 5.81527 20.75 7.315 20.75C8.81473 20.75 10.0629 19.6729 10.328 18.25H14.052C14.3171 19.6729 15.5653 20.75 17.065 20.75C18.5647 20.75 19.8129 19.6729 20.078 18.25H22C22.4142 18.25 22.75 17.9142 22.75 17.5C22.75 17.0858 22.4142 16.75 22 16.75V12.4047C22 11.9553 21.8655 11.5163 21.6137 11.1441L19.0674 7.37945C18.6489 6.76072 17.9506 6.39003 17.2037 6.39003H15.75V5.5C15.75 4.25736 14.7426 3.25 13.5 3.25H4.25ZM7.315 14.62C5.94831 14.62 4.79055 15.5145 4.39523 16.75H4.25C3.83579 16.75 3.5 16.4142 3.5 16V5.5C3.5 5.08579 3.83579 4.75 4.25 4.75H13.5C13.9142 4.75 14.25 5.08579 14.25 5.5V16.4706C14.2107 16.5615 14.1757 16.6547 14.1452 16.75H10.2348C9.83945 15.5145 8.68169 14.62 7.315 14.62ZM17.065 14.62C16.5944 14.62 16.1485 14.7261 15.75 14.9156V12.695L20.5 12.695V16.75H19.9848C19.5895 15.5145 18.4317 14.62 17.065 14.62ZM19.8373 11.195L15.75 11.195V7.89003H17.2037C17.4527 7.89003 17.6854 8.01359 17.8249 8.21983L19.8373 11.195ZM15.5 17.685C15.5 16.8207 16.2007 16.12 17.065 16.12C17.9293 16.12 18.63 16.8207 18.63 17.685C18.63 18.5493 17.9293 19.25 17.065 19.25C16.2007 19.25 15.5 18.5493 15.5 17.685ZM5.75 17.685C5.75 16.8207 6.45067 16.12 7.315 16.12C8.17933 16.12 8.88 16.8207 8.88 17.685C8.88 18.5493 8.17933 19.25 7.315 19.25C6.45067 19.25 5.75 18.5493 5.75 17.685Z"
                      fill="black"
                  ></path>
                </svg>
                <div class="flex flex-col">
                  <span class="font-medium text-base">Free Delivery</span>
                </div>
              </div>
            </div>
            <div class="flex justify-start items-center py-4 px-8 border border-gray-700 rounded-md">
              <div class="flex gap-x-6 items-center">
                <svg
                    width="40"
                    height="40"
                    viewBox="0 0 25 24"
                    fill="black"
                    xmlns="http://www.w3.org/2000/svg"
                >
                  <path
                      d="M3.13644 9.54175C3.02923 9.94185 3.26667 10.3531 3.66676 10.4603C4.06687 10.5675 4.47812 10.3301 4.58533 9.92998C5.04109 8.22904 6.04538 6.72602 7.44243 5.65403C8.83948 4.58203 10.5512 4.00098 12.3122 4.00098C14.0731 4.00098 15.7848 4.58203 17.1819 5.65403C18.3999 6.58866 19.3194 7.85095 19.8371 9.28639L18.162 8.34314C17.801 8.1399 17.3437 8.26774 17.1405 8.62867C16.9372 8.98959 17.0651 9.44694 17.426 9.65017L20.5067 11.3849C20.68 11.4825 20.885 11.5072 21.0766 11.4537C21.2682 11.4001 21.4306 11.2727 21.5282 11.0993L23.2629 8.01828C23.4661 7.65734 23.3382 7.2 22.9773 6.99679C22.6163 6.79358 22.159 6.92145 21.9558 7.28239L21.195 8.63372C20.5715 6.98861 19.5007 5.54258 18.095 4.464C16.436 3.19099 14.4033 2.50098 12.3122 2.50098C10.221 2.50098 8.1883 3.19099 6.52928 4.464C4.87027 5.737 3.67766 7.52186 3.13644 9.54175Z"
                      fill="black"
                  />
                  <path
                      d="M21.4906 14.4582C21.5978 14.0581 21.3604 13.6469 20.9603 13.5397C20.5602 13.4325 20.1489 13.6699 20.0417 14.07C19.5859 15.7709 18.5816 17.274 17.1846 18.346C15.7875 19.418 14.0758 19.999 12.3149 19.999C10.5539 19.999 8.84219 19.418 7.44514 18.346C6.2292 17.4129 5.31079 16.1534 4.79261 14.721L6.45529 15.6573C6.81622 15.8605 7.27356 15.7327 7.47679 15.3718C7.68003 15.0108 7.55219 14.5535 7.19127 14.3502L4.11056 12.6155C3.93723 12.5179 3.73222 12.4932 3.54065 12.5467C3.34907 12.6003 3.18662 12.7278 3.08903 12.9011L1.3544 15.9821C1.15119 16.3431 1.27906 16.8004 1.64 17.0036C2.00094 17.2068 2.45828 17.079 2.66149 16.718L3.42822 15.3562C4.05115 17.0054 5.12348 18.4552 6.532 19.536C8.19102 20.809 10.2237 21.499 12.3149 21.499C14.406 21.499 16.4387 20.809 18.0977 19.536C19.7568 18.263 20.9494 16.4781 21.4906 14.4582Z"
                      fill="black"
                  />
                </svg>
                <div class="flex flex-col">
                  <span class="font-medium text-base">Return Delivery</span>
                  <span class="text-sm">Free 30 Days Delivery Returns</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  <section class="container mx-auto space-y-8 max-w-screen-2xl">
    <section-title title="Product description"></section-title>
    <article class="tracking-wider w-full">
      <div class="w-full leading-8" v-html="productInfo.product?.main_description"></div>
    </article>
  </section>
  <section class="container mx-auto space-y-8 max-w-screen-2xl">
    <section-title title="Specifications"></section-title>
    <article class="w-1/2" v-if="productInfo.product?.specifications">
      <specs
          :grouped="productInfo.product?.specifications?.grouped"
          :separated="productInfo.product?.specifications?.separated"
      />
    </article>
  </section>
</template>

<style scoped>

</style>