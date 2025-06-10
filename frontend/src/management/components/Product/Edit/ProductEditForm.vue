<script setup>
import ProductPhotoTabsForm from "@/management/components/Product/Components/ProductPhotoTabsForm.vue";
import FocusedTextInput from "@/components/Default/Inputs/FocusedTextInput.vue";
import FocusedTextArea from "@/components/Default/Inputs/FocusedTextArea.vue";
import ProductPreviewImageForm from "@/management/components/Product/Other/ProductPreviewImageForm.vue";
import WarehouseInputs from "@/management/components/Warehouse/WarehouseInputs.vue";
import BrandsCombobox from "@/management/components/Product/Components/BrandsCombobox.vue";
import {computed, ref, toRaw} from "vue";
import CategoryChooser from "@/management/components/Product/Components/CategoryChooser.vue";
import DescriptionEditor from "@/management/components/Product/Components/DescriptionEditor.vue";
import ProductSpecs from "@/management/components/Product/Components/ProductSpecs.vue";
import ProductAttributesModal from "@/management/components/Product/Other/ProductAttributesModal.vue";
import SingleAttributeValueGenerator
  from "@/management/components/Product/AttributeGenerator/Single/SingleAttributeValueGenerator.vue";
import CombinedAttributeValueGenerator
  from "@/management/components/Product/AttributeGenerator/Combined/CombinedAttributeValueGenerator.vue";
import {ProductService} from "@/services/Product/ProductService.js";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultWarningSettings from "@/components/Default/Toasts/Default/defaultWarningSettings.js";
import {useRouter} from "vue-router";
import {useToastStore} from "@/stores/useToastStore.js";
import defaultErrorSetting from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";

const props = defineProps({
  product: Object,
  warehouse: Object,
  variations: Array,
});

const router = useRouter();
const toast = useToastStore();

const changedBrand = ref();
const changedCategory = ref();
const changedImages = ref([]);
const changedPreviewImage = ref({});
const productSpecs = ref([]);
const singleAttributesData = ref({});
const comboAttributesData = ref([]);
const options = ref({
  single: {},
  combined: {},
});

const existsVariations = ref(props.variations);

const isLoading = ref(false);

const errorsFromForm = ref([]);

const getOptions = (values) => {
  existsVariations.value = [];
  comboAttributesData.value = [];
  singleAttributesData.value = [];
  options.value = values;

  if (options.value?.combined && Object.keys(options.value.combined).length > 0) {
    props.product.is_combined_attributes = true;
  } else if (options.value?.single && Object.keys(options.value.single).length > 0) {
    props.product.is_combined_attributes = false;
  }
};

const optionsDeleted = () => {
  existsVariations.value = [];
  comboAttributesData.value = [];
  singleAttributesData.value = [];
};

const handleUpdatedSpecs = (newSpecs) => {
  productSpecs.value = newSpecs;
};

const handleUpdateAttributes = (data) => {
  singleAttributesData.value = data;
};

const handleUpdateComboAttributes = (data) => {
  comboAttributesData.value = data;
};

async function submitForm() {
  isLoading.value = true;

  let relationships = {
    category: {
      id: changedCategory.value?.id,
    },
    brand: {
      id: changedBrand.value?.id,
    },
  };

  relationships = addOptionalRelationships(relationships);

  await ProductService.editProduct(
      props.product,
      props.warehouse,
      relationships,
  )
      .then(async (response) => {
        await ProductService.editProductImages(props.product, changedImages.value, changedPreviewImage.value)
            .then(response => {
              toast.showToast(
                  "Product was successfully updated",
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
            .catch(e => {
              if (e.response?.status === 422) {
                toast.showToast(
                    e.response?.data?.message,
                    defaultErrorSetting,
                );
              } else {
                toast.showToast(
                    "Product image was not successfully uploads. Try again or contact us.",
                    defaultErrorSetting,
                );
                router.push({
                  name: "product-list",
                });
              }
            });
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

  if (
      (options.value?.combined &&
          Object.keys(options.value.combined).length > 0) ||
      comboAttributesData.value.length > 0
  ) {
    relations.product_variations_combinations = toRaw(
        comboAttributesData.value,
    );
  } else if (
      (options.value?.single && Object.keys(options.value.single).length > 0) ||
      singleAttributesData.value.length > 0
  ) {
    relations.product_variation = [toRaw(singleAttributesData.value)];
  } else {
    if (
        props.product.is_combined_attributes === true &&
        existsVariations.value.length > 0
    ) {
      relations.product_variations_combinations =
          reMappedExistsCombinedVariations.value;
    } else if (
        props.product.is_combined_attributes === false &&
        existsVariations.value.length > 0
    ) {
      relations.product_variation = [reMappedExistsSingleVariations.value];
    } else {
      props.product.is_combined_attributes = null;
    }
  }

  return relations;
}

const reMappedExistsSingleVariations = computed(() => {
  return {
    attribute_id: existsVariations.value[0]?.attributes.attribute_id || null,
    attribute_name: existsVariations.value[0]?.attributes.attribute_name || "",
    attribute_type: existsVariations.value[0]?.attributes.attribute_type_id || 0,
    attribute_view_type:
        existsVariations.value[0]?.attributes.attribute_type_id || 0,
    attributes: existsVariations.value.map((item) => ({
      value: item.attributes.value,
      quantity: item.attributes.quantity,
      price: item.attributes.price,
    })),
  };
});

const reMappedExistsCombinedVariations = computed(() => {
  return existsVariations.value.map((variation) => variation.attributes);
});
</script>

<template>
  <section class="container mx-auto my-14 flex flex-col gap-y-10">
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
              v-model="changedImages"
              :exists-images="product.images"
              class="w-full xl:w-auto"
          />

          <div class="w-full xl:w-1/3 space-y-2">
            <focused-text-input
                id="title"
                name="title"
                label="Title"
                v-model="product.title"
                required
                placeholder="Samsung S55"
            />

            <focused-text-area
                id="title_description"
                name="title_description"
                v-model="product.title_description"
                label="Title Description (short)"
                :rows="3"
                required
                placeholder="Discover the latest in electronic & smart appliance technology with Samsung. Find the next big thing from smartphones & tablets to laptops & tvs & more."
            />
            <brands-combobox
                v-model="changedBrand"
                :brand-id="product.brandId"
            />
            <category-chooser
                v-model="changedCategory"
                :category-id="product.categoryId"
            />
          </div>
        </div>
        <div class="my-6">
          <product-preview-image-form
              v-model="changedPreviewImage"
              :exists-preview-image="product.previewImage"
          />
        </div>
      </div>
      <div class="flex flex-col space-y-6">
        <span class="text-2xl font-semibold">Main Description</span>
        <description-editor v-model="product.main_description_markdown"/>
      </div>
      <div class="flex flex-col space-y-6">
        <span class="text-2xl font-semibold">Warehouse Information</span>
        <div class="ml-5 space-y-4">
          <div class="space-y-4">
            <span class="text-xl font-semibold">General</span>
            <warehouse-inputs
                v-model:product-article="product.article"
                v-model:total-quantity="warehouse.total_quantity"
                v-model:default-price="warehouse.price"
            />
          </div>
          <div class="flex flex-col space-y-4" v-if="changedCategory">
            <span class="text-xl font-semibold">Product Attributes</span>
            <product-attributes-modal
                @update-options="getOptions"
                :manually-generated="product.is_combined_attributes === null ? false : true"
                :category="changedCategory"
                @deleted-options="optionsDeleted"
            />
            <div
                v-show="
                options.single ||
                options.combined ||
                product.is_combined_attributes !== null
              "
            >
              <single-attribute-value-generator
                  v-if="
                  (options.single && Object.keys(options.single).length > 0) ||
                  product.is_combined_attributes === false
                "
                  :options="options"
                  :category="changedCategory"
                  :existing-attributes="existsVariations"
                  @update:attributes-data="handleUpdateAttributes"
              />
              <combined-attribute-value-generator
                  v-else-if="
                  (options.combined &&
                    Object.keys(options.combined).length > 0) ||
                  product.is_combined_attributes === true
                "
                  :options="options"
                  :category="changedCategory"
                  :existing-attributes="existsVariations"
                  @update:combinations="handleUpdateComboAttributes"
              />
            </div>
          </div>
          <div class="flex flex-col space-y-4" v-if="changedCategory">
            <span class="text-xl font-semibold">Product Specifications</span>
            <product-specs
                @update-product-specs="handleUpdatedSpecs"
                :initial-specs="product.specifications"
                :category="changedCategory"
            />
          </div>
        </div>
      </div>
      <div class="flex justify-center !mt-16">
        <button
            type="button"
            @click="submitForm"
            class="px-10 py-3 bg-blue-500 rounded-lg text-white hover:bg-yellow-600 w-1/2"
        >
          Update Product
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
    <div class="flex flex-col justify-center text-center gap-y-4 items-center">
      <div class="loader"></div>
      <p class="text-white font-bold text-xl">
        Updating product, please wait...
      </p>
    </div>
  </div>
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
