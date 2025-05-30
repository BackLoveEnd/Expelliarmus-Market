<script setup>
import {onMounted, ref, watch} from 'vue';
import {ContentManagementService} from '@/services/Different/ContentManagementService.js';
import defaultSuccessSettings from '@/components/Default/Toasts/Default/defaultSuccessSettings.js';
import defaultErrorSettings from '@/components/Default/Toasts/Default/defaultErrorSettings.js';
import {useToastStore} from '@/stores/useToastStore.js';
import Description from '@/components/Product/Arrival/Description.vue';
import DefaultContainer from '@/management/components/Main/DefaultContainer.vue';
import DefaultArrivalsPlaceholder from '@/management/components/ContentManagement/DefaultArrivalsPlaceholder.vue';

const arrivals = ref({});

const selectedArrivalId = ref(null);

let selectedArrival = ref({});

const fetchArrivals = async () => {
  await ContentManagementService.getAllArrivals()
      .then(response => {
        arrivals.value = Object.fromEntries(
            response.data.data.map(({attributes, ...rest}) => [attributes.position, {attributes, ...rest}])
        );
      })
      .catch(error => {
        if (error?.status === 404) {

        } else {
          console.error(error);
        }
      });
};

const toast = useToastStore();

const saveArrivals = async () => {
  const data = Object.values(arrivals.value).map(item => ({...item.attributes, id: item.id}));

  await ContentManagementService.uploadArrivalContent(data)
      .then(() => {
        toast.showToast('Arrivals saved successfully!', defaultSuccessSettings);
      })
      .catch(error => {
        if (error?.status === 422) {
          toast.showToast(error?.response?.data?.message, defaultErrorSettings);
        }

        toast.showToast('Unknown error. Try again or contact us.', defaultErrorSettings);
      });
};

const deleteArrival = async (id) => {
  if (arrivals.value.hasOwnProperty(id) && arrivals.value[id].id !== null) {
    await ContentManagementService.deleteArrivalContent(arrivals.value[id].id)
        .then(() => {
          delete arrivals.value[id];

          toast.showToast('Arrival deleted successfully!', defaultSuccessSettings);
        })
        .catch(error => {
          toast.showToast('Unknown error. Try again or contact us.', defaultErrorSettings);
        });
  } else {
    setDefaultArrival(id);
  }
};

function imageSizeByPosition(position) {
  if (position === 1) {
    return '744x720';
  }

  if (position === 2) {
    return '744x336';
  }

  return '348x336';
}

const handleFileUpload = (event, id) => {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {

    if (!arrivals.value.hasOwnProperty(id)) return;

    const img = new Image();
    img.src = e.target.result;
    img.onload = () => {
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');

      const [width, height] = imageSizeByPosition(id).split('x').map(Number);
      canvas.width = width;
      canvas.height = height;

      ctx.drawImage(img, 0, 0, width, height);

      arrivals.value[id].attributes.image_url = canvas.toDataURL('image/jpeg', 1);
    };
  };
  reader.readAsDataURL(file);
};

function setDefaultArrival(position) {
  selectedArrival.value = {
    id: null,
    attributes: {
      position: position,
      content: {
        title: 'Default Title',
        body: 'Default Description',
      },
      arrival_url: '',
      image_url: `https://dummyimage.com/${imageSizeByPosition(position)}/000/fff`,
    },
  };
}

onMounted(fetchArrivals);

watch(
    selectedArrivalId,
    (newSelectedId) => {
      if (typeof arrivals.value[newSelectedId] !== 'undefined') {
        selectedArrival.value = arrivals.value[newSelectedId];
      } else {
        setDefaultArrival(newSelectedId);
      }
    }
);

watch(
    (selectedArrival),
    (newSelectedArrival) => {
      arrivals.value[newSelectedArrival.attributes.position] = newSelectedArrival;
    },
    {deep: true}
);
</script>

<template>
  <default-container>
    <div class="my-14 space-y-8">
      <section class="container mx-auto">
        <h1 class="font-semibold text-4xl mb-4">New Arrivals Management</h1>
        <div class="flex flex-col items-start mt-12 space-y-8">
          <h2 class="text-xl font-semibold">Arrivals</h2>
          <span class="text-sm">Note: On public home page, new arrivals will be visible only when all 3 sections are filled.</span>
          <form class="max-w-max mx-4">
            <label for="arrivalsId" class="block mb-2 text-m font-semibold">Select an option</label>
            <select v-model="selectedArrivalId" id="arrivalsId"
                    class="bg-gray-50 border text-gray-900 text-sm rounded-lg p-2.5">
              <option selected="true" disabled="disabled">Choose arrival position</option>
              <option v-for="arrival in 4" :key="arrival" :value="arrival">
                {{ arrival }}
              </option>
            </select>
            <div v-if="selectedArrival.attributes">
              <label class="block mt-4 mb-2 text-medium font-medium">Upload file</label>
              <input type="file"
                     class="block text-sm border rounded-lg cursor-pointer"
                     @change="handleFileUpload($event, selectedArrivalId)"
              />


              <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">
                SVG, PNG, JPG or GIF (MAX. {{ imageSizeByPosition(selectedArrivalId) }}).
              </p>

              <label for="card-title" class="block mt-4 mb-2 text-sm font-medium">Card title</label>
              <div class="flex items-center gap-x-4 flex-wrap">
                <input type="text" id="card-title" v-model="selectedArrival.attributes.content.title"
                       class="bg-gray-50 border text-sm rounded-lg p-2.5"/>
              </div>
              <label for="card-description" class="block mt-4 mb-2 text-sm font-medium">Card description</label>
              <textarea id="card-description" v-model="selectedArrival.attributes.content.body" rows="4"
                        class="block p-2.5 w-full text-sm bg-gray-50 border rounded-lg"></textarea>

              <label for="card-link" class="block mt-4 mb-2 text-sm font-medium">Product link</label>
              <input type="text" id="card-link" v-model="selectedArrival.attributes.arrival_url"
                     class="bg-gray-50 border text-sm rounded-lg p-2.5"/>

            </div>
          </form>
          <div class="flex gap-4 mt-4" v-if="selectedArrival.attributes">
            <button
                @click="saveArrivals"
                class="bg-blue-500 text-white px-4 py-2 rounded"
            >
              Save
            </button>
            <button
                @click="deleteArrival(selectedArrivalId)"
                class="bg-red-500 text-white px-3 py-1 rounded"
            >
              Delete
            </button>
          </div>
        </div>

      </section>
      <hr/>
      <section
          class="container mx-auto space-y-4"
          v-if="Object.keys(arrivals).length > 0"
      >
        <h2 class="text-xl font-semibold text-center">Arrivals Preview</h2>
        <div class="grid grid-cols-2 gap-12">
          <div class="relative" v-if="arrivals.hasOwnProperty(1)">
            <h2 class="text-l font-semibold text-center">Position-1</h2>
            <img class="w-full" :src="arrivals[1].attributes.image_url" alt="First arrival"/>
            <Description
                :description="arrivals[1].attributes.content.body"
                :name="arrivals[1].attributes.content.title"
                :link="arrivals[1].attributes.arrival_url"/>
          </div>
          <div class="relative" v-else>
            <default-arrivals-placeholder
                :content="{title: 'Default Title', body: 'Default Desc', url: '#'}"
                :position="1"
                :image="'https://dummyimage.com/744x720/000/fff'"
            />
          </div>
          <div class="grid grid-cols-2 grid-rows-2 gap-6">
            <div class="col-span-2 h-full relative" v-if="arrivals.hasOwnProperty(2)">
              <h2 class="text-l font-semibold text-center">Position-2</h2>
              <img :src="arrivals[2].attributes.image_url" alt="Second arrival"/>
              <Description
                  :description="arrivals[2].attributes.content.body"
                  :name="arrivals[2].attributes.content.title"
                  :link="arrivals[2].attributes.arrival_url"/>
            </div>
            <div v-else class="col-span-2 h-full relative">
              <default-arrivals-placeholder
                  :content="{title: 'Default Title', body: 'Default Desc', url: '#'}"
                  :position="2"
                  :image="'https://dummyimage.com/744x336/000/fff'"
              />
            </div>
            <div class="h-full relative" v-if="arrivals.hasOwnProperty(3)">
              <h2 class="text-l font-semibold text-center">Position-3</h2>
              <img :src="arrivals[3].attributes.image_url" alt="Third arrival"/>
              <Description
                  :description="arrivals[3].attributes.content.body"
                  :name="arrivals[3].attributes.content.title"
                  :link="arrivals[3].attributes.arrival_url"/>
            </div>
            <div v-else class="h-full relative">
              <default-arrivals-placeholder
                  :content="{title: 'Default Title', body: 'Default Desc', url: '#'}"
                  :position="3"
                  :image="'https://dummyimage.com/348x336/000/fff'"
              />
            </div>
            <div class="h-full relative" v-if="arrivals.hasOwnProperty(4)">
              <h2 class="text-l font-semibold text-center">Position-4</h2>
              <img :src="arrivals[4].attributes.image_url" alt="Fourth arrival"/>
              <Description
                  :description="arrivals[4].attributes.content.body"
                  :name="arrivals[4].attributes.content.title"
                  :link="arrivals[4].attributes.arrival_url"/>
            </div>
            <div v-else class="h-full relative">
              <default-arrivals-placeholder
                  :content="{title: 'Default Title', body: 'Default Desc', url: '#'}"
                  :position="4"
                  :image="'https://dummyimage.com/348x336/000/fff'"
              />
            </div>
          </div>
        </div>
      </section>
      <div class="container mx-auto space-y-4" v-else>
        <p>Arrivals not found.</p>
      </div>
    </div>
  </default-container>
</template>

<style scoped>
button {
  transition: background-color 0.3s;
}

button:hover {
  opacity: 0.9;
}
</style>
