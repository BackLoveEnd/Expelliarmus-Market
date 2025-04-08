<template>
  <div class="space-y-4">
    <div
        v-for="(group, groupIndex) in productSpecs.data"
        :key="groupIndex"
        class="mr-2"
    >
      <div v-if="group.group !== null" class="space-y-4">
        <div class="flex flex-wrap gap-4 items-end !text-black">
          <div class="flex flex-col gap-y-2">
            <label :for="`autocomplete-group-${groupIndex}`">Group</label>
            <AutoComplete
                :id="`autocomplete-group-${groupIndex}`"
                option-label="group"
                v-model="group.group"
                :suggestions="groupNames"
                placeholder="Choose group"
                empty-search-message="No groups found."
                @complete="searchGroups"
                @update:model-value="
                (value) => updateGroupSpecs(groupIndex, value)
              "
                dropdown
            />
          </div>

          <button
              type="button"
              @click="removeGroup(groupIndex)"
              class="bg-red-500 p-2 text-white rounded-md hover:bg-red-800 ml-4"
          >
            Delete group
          </button>
        </div>

        <div
            v-for="(spec, specIndex) in group.specifications"
            :key="specIndex"
            class="space-y-4 ml-5"
        >
          <div class="flex flex-wrap gap-4 items-start flex-col sm:flex-row">
            <div class="flex flex-col gap-y-2">
              <label :for="`autocomplete-spec-${groupIndex}-${specIndex}`"
              >Specification</label
              >
              <AutoComplete
                  :id="`autocomplete-spec-${groupIndex}-${specIndex}`"
                  option-label="spec_name"
                  v-model="spec.spec_name"
                  :suggestions="filteredAttributes"
                  placeholder="Specification"
                  empty-search-message="Specifications not found"
                  @complete="searchSpecs(groupIndex, $event)"
                  @update:model-value="updateSpec(groupIndex, specIndex, $event)"
                  dropdown
              />
            </div>

            <div class="flex flex-col gap-4 w-full sm:w-auto">
              <div
                  v-for="(val, valIndex) in spec.value"
                  :key="valIndex"
                  class="flex gap-4 items-end flex-col sm:flex-row"
              >
                <focused-text-input
                    v-model="spec.value[valIndex]"
                    id="value"
                    name="Value"
                    label="Value"
                    required
                    class="w-full sm:w-60"
                />
                <button
                    v-if="valIndex === 0"
                    type="button"
                    @click="addSpecValue(groupIndex, specIndex)"
                    class="bg-blue-500 p-2 text-white rounded-md hover:bg-blue-800"
                >
                  Add value
                </button>
                <button
                    v-if="valIndex === 0"
                    type="button"
                    @click="removeSpecFromGroup(groupIndex, specIndex)"
                    class="bg-red-500 p-2 text-white rounded-md hover:bg-red-800"
                >
                  Delete specification
                </button>
                <button
                    v-if="valIndex > 0"
                    type="button"
                    @click="removeSpecValue(groupIndex, specIndex, valIndex)"
                    class="bg-red-500 p-2 text-white rounded-md hover:bg-red-800"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <button
            type="button"
            @click="addGroupField(groupIndex)"
            class="px-10 py-3 bg-gray-500 rounded-lg text-white hover:bg-gray-700 mt-4 sm:mt-6"
        >
          Add new specification to group
        </button>
      </div>
    </div>

    <div class="space-y-4">
      <div
          v-for="(spec, index) in unGroupedSpecs"
          :key="index"
          class="space-y-4"
      >
        <div class="flex gap-4 items-start">
          <div class="flex flex-col gap-2">
            <label>Specification</label>
            <AutoComplete
                option-label="spec_name"
                v-model="spec.spec_name"
                :suggestions="filteredAttributes"
                placeholder="Choose specification"
                empty-search-message="Specifications not found."
                @complete="searchSpecs(null, $event)"
                @update:model-value="(value) => updateUnGroupedSpec(index, value)"
                dropdown
            />
          </div>
          <div class="flex gap-2 flex-col">
            <div
                v-for="(val, valIndex) in spec.value"
                :key="valIndex"
                class="flex gap-4 items-end w-full"
                :class="{ 'justify-between': valIndex === 0 }"
            >
              <focused-text-input
                  v-model="spec.value[valIndex]"
                  id="value"
                  name="Value"
                  label="Value"
                  required
                  class="w-full sm:w-60"
              />
              <button
                  v-if="valIndex === 0"
                  type="button"
                  @click="addUnGroupedSpecValue(index)"
                  class="bg-blue-500 p-2 text-white rounded-md hover:bg-blue-800"
              >
                Add value
              </button>
              <button
                  v-if="valIndex === 0"
                  type="button"
                  @click="removeUnGroupedSpec(index)"
                  class="bg-red-500 p-2 text-white rounded-md hover:bg-red-800"
              >
                Delete specification
              </button>
              <button
                  v-if="valIndex > 0"
                  type="button"
                  @click="removeUnGroupedSpecValue(index, valIndex)"
                  class="bg-red-500 p-2 text-white rounded-md hover:bg-red-800"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-wrap gap-4 justify-between sm:justify-start">
      <button
          type="button"
          @click="addGroup"
          class="px-10 py-3 bg-blue-500 rounded-lg text-white hover:bg-blue-700 sm:w-auto w-full"
      >
        Add group
      </button>
      <button
          type="button"
          @click="addUnGroupedSpec"
          class="px-10 py-3 bg-blue-500 rounded-lg text-white hover:bg-blue-700 sm:w-auto w-full"
      >
        Add specification
      </button>
    </div>
  </div>
</template>

<script setup>
import {computed, onMounted, reactive, ref, watch} from "vue";
import FocusedTextInput from "@/components/Default/Inputs/FocusedTextInput.vue";
import {ProductService} from "@/services/Product/ProductService.js";
import {AutoComplete} from "primevue";

const props = defineProps({
  category: Object,
  initialSpecs: null,
});

const productSpecs = reactive({data: []});
const unGroupedSpecs = ref([]);
const groups = ref([]);
const separatedSpecs = ref([]);
const selectedGroupSpecs = ref({});
const filteredAttributes = ref([]);
const groupNames = ref([]);

const emit = defineEmits(["update-product-specs"]);

const addGroup = () => {
  productSpecs.data.push({
    group: "",
    specifications: [{spec_name: "", value: [""]}],
  });
};

const addUnGroupedSpec = () => {
  unGroupedSpecs.value.push({id: null, spec_name: "", value: [""]});
};

const addGroupField = (groupIndex) => {
  productSpecs.data[groupIndex].specifications.push({
    spec_name: "",
    value: [""],
  });
};

const addUnGroupedSpecValue = (index) => {
  unGroupedSpecs.value[index].value.push("");
};

const addSpecValue = (groupIndex, specIndex) => {
  productSpecs.data[groupIndex].specifications[specIndex].value.push("");
};

const removeUnGroupedSpec = (index) => {
  unGroupedSpecs.value.splice(index, 1);
};

const removeSpecFromGroup = (groupIndex, specIndex) => {
  productSpecs.data[groupIndex].specifications.splice(specIndex, 1);
};

const removeUnGroupedSpecValue = (index, valIndex) => {
  unGroupedSpecs.value[index].value.splice(valIndex, 1);
};

const removeSpecValue = (groupIndex, specIndex, valIndex) => {
  productSpecs.data[groupIndex].specifications[specIndex].value.splice(
      valIndex,
      1,
  );
};

const removeGroup = (groupIndex) => {
  productSpecs.data.splice(groupIndex, 1);
};

const updateGroupSpecs = (groupIndex, selectedGroup) => {
  if (!selectedGroup) return;

  const group = groups.value.find((g) => g.group === selectedGroup.group);
  if (group) {
    selectedGroupSpecs.value[groupIndex] = group.specifications;
    productSpecs.data[groupIndex].group = group.group;
    productSpecs.data[groupIndex].specifications = group.specifications.map(
        (spec) => ({id: spec.id, spec_name: spec.spec_name, value: [""]}),
    );
  } else {
    selectedGroupSpecs.value[groupIndex] = [];
    productSpecs.data[groupIndex].specifications = [
      {id: null, spec_name: null, value: [""]},
    ];
  }
};

const searchGroups = (event) => {
  const query = event.query.toLowerCase();
  groupNames.value = groups.value.filter((g) =>
      g.group.toLowerCase().includes(query),
  );
};

const searchSpecs = (groupIndex, event) => {
  const query = event.query.toLowerCase();
  const specs =
      groupIndex !== null
          ? selectedGroupSpecs.value[groupIndex] || []
          : separatedSpecs.value;

  filteredAttributes.value = specs.filter((spec) =>
      spec.spec_name.toLowerCase().includes(query),
  );
};

const updateSpec = (groupIndex, specIndex, selectedSpec) => {
  if (!selectedSpec) return;

  const isGroupBasedSpec = productSpecs.data[groupIndex].group !== null;

  if (isGroupBasedSpec) {
    const spec = separatedSpecs.value.find(
        (s) => s.spec_name === selectedSpec.spec_name,
    );
    if (spec) {
      console.log(spec);
      productSpecs.data[groupIndex].specifications[specIndex].id = spec.id;
      productSpecs.data[groupIndex].specifications[specIndex].spec_name =
          spec.spec_name;
    } else {
      productSpecs.data[groupIndex].specifications[specIndex].id = null;
      productSpecs.data[groupIndex].specifications[specIndex].spec_name =
          selectedSpec;
    }
  } else {
    const spec = separatedSpecs.value.find(
        (s) => s.spec_name === selectedSpec.spec_name,
    );
    console.log(spec);
    if (spec) {
      unGroupedSpecs.value[groupIndex].id = spec.id;
      unGroupedSpecs.value[groupIndex].spec_name = spec.spec_name;
    } else {
      unGroupedSpecs.value[groupIndex].id = selectedSpec?.id;
      unGroupedSpecs.value[groupIndex].spec_name = selectedSpec?.name;
    }
  }
};

const updateUnGroupedSpec = (index, selectedSpec) => {
  if (!selectedSpec) return;

  const spec = separatedSpecs.value.find(
      (s) => s.spec_name === selectedSpec.spec_name,
  );

  if (spec) {
    unGroupedSpecs.value[index] = {
      id: spec.id,
      spec_name: spec.spec_name,
      value: [""],
    };
  } else {
    unGroupedSpecs.value[index] = {
      id: null,
      spec_name: selectedSpec,
      value: [""],
    };
  }
};

onMounted(async () => {
  await fetchSpecs();

  if (props.initialSpecs?.grouped) {
    productSpecs.data = props.initialSpecs.grouped.map((group) => ({
      group: group.group,
      specifications: group.specifications.map((spec) => ({
        id: spec.id,
        spec_name: spec.specification,
        value: spec.value,
      })),
    }));
  }

  if (props.initialSpecs?.separated) {
    unGroupedSpecs.value = props.initialSpecs.separated.specifications.map(
        (spec) => ({
          id: spec.id,
          spec_name: spec.specification,
          value: spec.value,
        }),
    );
  }
});

watch(
    () => props.category.id,
    async () => {
      await fetchSpecs();
    },
);

const combinedSpecs = computed(() => {
  const grouped = productSpecs.data.map((group) => ({
    group: group.group,
    specifications: group.specifications,
  }));

  if (unGroupedSpecs.value.length > 0) {
    grouped.push({
      group: null,
      specifications: unGroupedSpecs.value,
    });
  }

  return grouped;
});

watch(combinedSpecs, (newVal) => {
  emit("update-product-specs", newVal);
});

async function fetchSpecs() {
  await ProductService.getProductSpecificationsByCategory(props.category.id)
      .then((response) => {
        groups.value = response.data.data.attributes.grouped;
        separatedSpecs.value = response.data.data.attributes.separated;
      })
      .catch((e) => {
      });
}
</script>

<style>
.p-autocomplete {
  @apply w-80;
}
</style>
