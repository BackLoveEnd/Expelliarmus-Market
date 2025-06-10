<script setup>
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import ProductPhotoTabsForm from "@/management/components/Product/Components/ProductPhotoTabsForm.vue";
import BrandsCombobox from "@/management/components/Product/Components/BrandsCombobox.vue";
import FocusedTextArea from "@/components/Default/Inputs/FocusedTextArea.vue";
import FocusedTextInput from "@/components/Default/Inputs/FocusedTextInput.vue";
import CategoryChooser from "@/management/components/Product/Components/CategoryChooser.vue";
import DescriptionEditor from "@/management/components/Product/Components/DescriptionEditor.vue";
import {ref, toRaw} from "vue";
import WarehouseInputs from "@/management/components/Warehouse/WarehouseInputs.vue";
import ProductAttributesModal from "@/management/components/Product/Other/ProductAttributesModal.vue";
import ProductSpecs from "@/management/components/Product/Components/ProductSpecs.vue";
import {ProductService} from "@/services/Product/ProductService.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import {useToastStore} from "@/stores/useToastStore.js";
import defaultErrorSetting from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import {useRouter} from "vue-router";
import ProductPreviewImageForm from "@/management/components/Product/Other/ProductPreviewImageForm.vue";
import SingleAttributeValueGenerator
  from "@/management/components/Product/AttributeGenerator/Single/SingleAttributeValueGenerator.vue";
import CombinedAttributeValueGenerator
  from "@/management/components/Product/AttributeGenerator/Combined/CombinedAttributeValueGenerator.vue";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultWarningSettings from "@/components/Default/Toasts/Default/defaultWarningSettings.js";

const options = ref({
  single: {},
  combined: {},
});

const withCombinedAttributes = ref(null);

const isLoading = ref(false);

const errorsFromForm = ref([]);

const toast = useToastStore();

const router = useRouter();

const getOptions = (values) => {
  options.value = values;
};

const productData = ref({
  title: null,
  title_description: null,
  main_description: null,
  product_article: null,
  total_quantity: null,
  price: null,
  is_combined_attributes: null,
});

const brand = ref();

const category = ref();

const singleAttributesData = ref({});

const comboAttributesData = ref([]);

const productSpecs = ref([]);

const images = ref([]);

const previewImage = ref({});

const handleUpdateAttributes = (data) => {
  singleAttributesData.value = data;
};

const handleUpdateComboAttributes = (data) => {
  comboAttributesData.value = data;
};

const handleUpdatedSpecs = (newSpecs) => {
  productSpecs.value = newSpecs;
};

async function submitForm() {
  isLoading.value = true;

  let relationships = {
    category: {
      id: category.value?.id,
    },
    brand: {
      id: brand.value?.id,
    },
  };

  productData.value.is_combined_attributes =
      Object.keys(options.value.combined).length > 0
          ? true
          : Object.keys(options.value.single).length > 0
              ? false
              : null;

  relationships = addOptionalRelationships(relationships);

  await ProductService.createProduct(productData, relationships)
      .then(async (response) => {
        if (response?.data?.data?.id) {
          await ProductService.uploadImagesForProduct(
              response.data.data.id,
              images.value,
              previewImage.value,
          )
              .then((response) => {
                toast.showToast(
                    "Product was successfully created",
                    defaultSuccessSettings,
                );

                if (response?.status === 206) {
                  toast.showToast(response?.data?.message, defaultWarningSettings);
                }
                if (response?.status === 200) {
                  toast.showToast(response?.data?.message, defaultSuccessSettings);
                }

                router.push({
                  name: "product-list",
                });
              })
              .catch((e) => {
                toast.showToast(
                    "Product image was not successfully uploads. Try again or contact us.",
                    defaultErrorSetting,
                );
                router.push({
                  name: "product-list",
                });
              });
        }
      })
      .catch((e) => {
        if (e.response.data?.errors) {
          errorsFromForm.value = useJsonApiFormatter().fromJsonApiErrorsFields(
              e.response.data.errors,
          );
        }
      })
      .finally(() => (isLoading.value = false));
}

function addOptionalRelationships(relations) {
  if (productSpecs.value.length > 0) {
    relations.product_specs = productSpecs.value;
  }

  if (Object.keys(options.value.combined).length > 0) {
    relations.product_variations_combinations = toRaw(
        comboAttributesData.value,
    );
  } else if (Object.keys(options.value.single).length > 0) {
    relations.product_variation = [toRaw(singleAttributesData.value)];
  }

  return relations;
}
</script>

<template>
  <default-container>
    <section class="container mx-auto my-14 flex flex-col gap-y-10">
      <div>
        <h1 class="font-semibold text-4xl">Create Product Form</h1>
      </div>
      <form class="space-y-6" method="post">
        <div class="mt-4 flex flex-col">
          <span class="text-2xl font-semibold mb-6">General Information</span>
          <span class="mb-2"
          >Maximum 4 photos. Please, use ~576x712 photo size.</span
          >
          <div
              class="flex xl:flex-nowrap flex-wrap items-center justify-between gap-4"
          >
            <product-photo-tabs-form
                v-model="images"
                class="w-full xl:w-auto"
            />

            <div class="w-full xl:w-1/3 space-y-2">
              <focused-text-input
                  id="title"
                  name="title"
                  label="Title"
                  v-model="productData.title"
                  required
                  placeholder="Samsung S55"
              />

              <focused-text-area
                  id="title_description"
                  name="title_description"
                  v-model="productData.title_description"
                  label="Title Description (short)"
                  :rows="3"
                  required
                  placeholder="Discover the latest in electronic & smart appliance technology with Samsung. Find the next big thing from smartphones & tablets to laptops & tvs & more."
              />
              <brands-combobox v-model="brand"/>
              <category-chooser v-model="category"/>
            </div>
          </div>
          <div class="my-6">
            <product-preview-image-form v-model="previewImage"/>
          </div>
        </div>
        <div class="flex flex-col space-y-6">
          <span class="text-2xl font-semibold">Main Description</span>
          <description-editor v-model="productData.main_description"/>
        </div>
        <div class="flex flex-col space-y-6">
          <span class="text-2xl font-semibold">Warehouse Information</span>
          <div class="ml-5 space-y-4">
            <div class="space-y-4">
              <span class="text-xl font-semibold">General</span>
              <warehouse-inputs
                  v-model:product-article="productData.product_article"
                  v-model:total-quantity="productData.total_quantity"
                  v-model:default-price="productData.price"
              />
            </div>
            <div class="flex flex-col space-y-4" v-if="category">
              <span class="text-xl font-semibold">Product Attributes</span>
              <product-attributes-modal
                  @update-options="getOptions"
                  :category="category"
              />
              <div v-show="options.single || options.combined">
                <single-attribute-value-generator
                    v-if="
                    options.single && Object.keys(options.single).length > 0
                  "
                    :options="options"
                    :category="category"
                    @update:attributes-data="handleUpdateAttributes"
                />
                <combined-attribute-value-generator
                    v-else-if="
                    options.combined && Object.keys(options.combined).length > 0
                  "
                    :options="options"
                    :category="category"
                    @update:combinations="handleUpdateComboAttributes"
                />
              </div>
            </div>
            <div class="flex flex-col space-y-4" v-if="category">
              <span class="text-xl font-semibold">Product Specifications</span>
              <product-specs
                  @update-product-specs="handleUpdatedSpecs"
                  :category="category"
              />
            </div>
          </div>
        </div>
        <div class="flex justify-center !mt-16">
          <button
              type="button"
              @click="submitForm"
              class="px-10 py-3 bg-gray-500 rounded-lg text-white hover:bg-yellow-600 w-1/2"
          >
            Create Product
          </button>
        </div>
      </form>
      <section
          v-if="errorsFromForm.length"
          class="w-1/2 flex justify-center mx-auto bg-red-500 py-6 rounded-md text-gray-200"
      >
        <div class="flex flex-col space-y-4">
          <p v-for="error in errorsFromForm">
            {{ Object.values(error)[0] }}
          </p>
        </div>
      </section>
    </section>
    <div
        v-if="isLoading"
        class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 z-50"
    >
      <div
          class="flex flex-col justify-center text-center gap-y-4 items-center"
      >
        <div class="loader"></div>
        <p class="text-white font-bold text-xl">
          Creating product, please wait...
        </p>
      </div>
    </div>
  </default-container>
</template>

<style scoped>
.loader {
  border: 5px solid rgba(255, 255, 255, 0.3);
  border-top: 5px solid #e8a439;
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
