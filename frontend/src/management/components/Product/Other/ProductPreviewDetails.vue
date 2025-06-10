<script setup>
import Description from "@/components/Product/Main/Description.vue";
import SectionTitle from "@/components/Default/SectionTitle.vue";
import Specs from "@/components/Product/Main/Specs.vue";
import ProductPhotoTabs from "@/shop/views/Product/ProductPhotoTabs.vue";
import BreadCrumbs from "@/components/Default/BreadCrumbs.vue";
import {computed, reactive, ref} from "vue";
import {ProductService} from "@/services/Product/ProductService.js";
import SingleVariationsViewer from "@/components/Product/Main/SingleVariationsViewer.vue";
import CombinedVariationsViewer from "@/components/Product/Main/CombinedVariationsViewer.vue";
import {useRouter} from "vue-router";

const props = defineProps({
  id: Number | String,
  slug: String,
});

const router = useRouter();

let product = reactive({
  id: null,
  title: null,
  slug: null,
  article: null,
  main_description: null,
  title_description: null,
  images: null,
  specifications: {
    grouped: [],
    separated: {},
  },
});

const previewVariations = ref();

const category = ref({});

const brand = ref({});

const specifications = ref([]);

const variations = ref([]);

const links = ref([]);

const quantity = ref(1);

const price = ref(0);

let selectedVariation = reactive({
  id: null,
  price: null,
  discount: null
});

const priceDependOnQuantity = computed(() =>
    (price.value * quantity.value).toFixed(2),
);

const pricePerUnit = computed(() => price.value.toFixed(2));

const imagesUrls = computed(() => {
  return product.images.map((image) => image.image_url);
});

const handleSelectedVariation = (variation) => {
  setSelectedVariation(variation);

  if (selectedVariation.discount !== null) {
    price.value = parseFloat(selectedVariation.discount.discount_price);
  } else {
    price.value = parseFloat(variation.attributes.price);
  }
};

function setSelectedVariation(variation) {
  selectedVariation.price = variation.attributes.price;
  selectedVariation.id = variation.attributes.id;
  selectedVariation.discount = variation.attributes.discount;
}

async function getProduct() {
  try {
    const response = await ProductService.getProductPreview(
        props.id,
        props.slug,
    );

    Object.assign(product, response.product);

    brand.value = response.brand;
    category.value = response.category;
    specifications.value = response.specifications;
    variations.value = response.variations;
    previewVariations.value = response.previewVariations;

    links.value = [
      {url: "/management/products", name: "All Products"},
      {url: "", name: product.title},
    ];
  } catch (e) {
    if (e.response?.status === 404) {
      router.back();
    }
  }
}

await getProduct();
</script>

<template>
  <div class="space-y-20 mt-5">
    <section class="container mx-auto max-w-screen-2xl">
      <bread-crumbs :links="links"></bread-crumbs>
    </section>
    <section class="container mx-auto">
      <div class="flex justify-between">
        <product-photo-tabs :images="imagesUrls"/>
        <div class="flex flex-col gap-y-8 items-start min-w-[35%]">
          <div class="flex flex-col gap-y-4 w-full">
            <description
                :price="priceDependOnQuantity"
                :price-per-unit="pricePerUnit"
                :title="product.title"
                :title-desc="product.title_description"
                :article="product.article"
            >
            </description>
          </div>
          <div class="flex flex-col gap-y-8" v-if="previewVariations">
            <combined-variations-viewer
                v-if="Array.isArray(previewVariations)"
                :previewed-variations="previewVariations"
                :variations="variations"
                @selected-option="handleSelectedVariation"
            />
            <single-variations-viewer
                v-else
                :variations="variations"
                :previewed-variation="previewVariations"
                @selected-option="handleSelectedVariation"
            />
          </div>
        </div>
      </div>
    </section>
  </div>
  <section class="container mx-auto space-y-8 max-w-screen-2xl">
    <section-title title="Product description"></section-title>
    <article class="tracking-wider w-full">
      <div class="w-full leading-8" v-html="product.main_description"></div>
    </article>
  </section>
  <section class="container mx-auto space-y-8 max-w-screen-2xl">
    <section-title title="Specifications"></section-title>
    <article class="w-1/2">
      <specs
          :grouped="product.specifications.grouped"
          :separated="product.specifications.separated"
      />
    </article>
  </section>
</template>

<style scoped></style>
