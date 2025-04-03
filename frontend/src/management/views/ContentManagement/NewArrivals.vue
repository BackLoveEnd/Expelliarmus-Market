<script setup>
import {computed, ref, watch, onMounted} from "vue";
import {ContentManagementService} from "@/services/ContentManagementService";
import DefaultContainer from "@/management/components/Main/DefaultContainer.vue";
import Description from "@/components/Product/Arrival/Description.vue";
import defaultSuccessSettings from "@/components/Default/Toasts/Default/defaultSuccessSettings.js";
import defaultErrorSettings from "@/components/Default/Toasts/Default/defaultErrorSettings.js";
import {useToastStore} from "@/stores/useToastStore.js";
import Colors from "@/components/Product/Main/Colors.vue";

const toast = useToastStore();
const defaultArrivals = [
  {
    id: 0,
    image: "https://dummyimage.com/744x720/000/fff",
    title: "Title(1)",
    description: "Desc.1",
    link: "",
    imgSize: "744x720"
  },
  {
    id: 1,
    image: "https://dummyimage.com/744x336/000/fff",
    title: "Title(2)",
    description: "Desc.2",
    link: "",
    imgSize: "744x336"
  },
  {
    id: 2,
    image: "https://dummyimage.com/348x336/000/fff",
    title: "Title(3)",
    description: "Desc.3",
    link: "",
    imgSize: "348x336"
  },
  {
    id: 3,
    image: "https://dummyimage.com/348x336/000/fff",
    title: "Title(4)",
    description: "Desc.4",
    link: "",
    imgSize: "348x336"
  }
]
const arrivals = ref([...defaultArrivals]);
const selectedArrivalId = ref(null);

const selectedArrival = computed(() =>
    arrivals.value.find(arrival => arrival.id === selectedArrivalId.value)
);

const loadFromStorage = () => {
  const savedData = localStorage.getItem("arrivals");
  if (savedData) {
    arrivals.value = JSON.parse(savedData);
  } else {
    fetchArrivals();
  }
};

const saveToStorage = () => {
  localStorage.setItem("arrivals", JSON.stringify(arrivals.value));
};

const fetchArrivals = () => {
  ContentManagementService.getAllArrivals()
      .then(response => {
        if (response.data.length) {
          arrivals.value = response.data.map((arrival, index) => ({
            id: index,
            image: arrival.exists_image_url || `https://dummyimage.com/${arrival.imgSize}/000/fff`,
              title: arrival.content?.title || "",
              description: arrival.content?.body || "",
              link: arrival.link || "",
              imgSize: arrival.imgSize
        }));
        }
        saveToStorage();
      })
      .catch(error => {
        console.error("Fetch arrivals failed:", error);
      })
};


const saveArrivals = () => {
  const formattedData = arrivals.value.map(arrival => {
    const hasValidImage = arrival.image && typeof arrival.image === "string" && arrival.image.startsWith("data:image");
    const hasValidUrl = arrival.link && typeof arrival.link === "string" && arrival.link.length > 0;

    if (!hasValidImage && !hasValidUrl) {
      console.error("Invalid arrival data:", arrival);
      return null;
    }

    return {
      position: arrival.id + 1,
      arrival_url: arrival.link || "http://expelliarmus.com/",
      exists_image_url: arrival.image.startsWith("http") ? arrival.image : "",
      content: {
        title: arrival.title,
        body: arrival.description
      },
      file: hasValidImage ? arrival.image : undefined
    };
  }).filter(Boolean);

  ContentManagementService.uploadArrivalContent(formattedData)
      .then(() => {
        toast.showToast("Arrivals saved successfully!", defaultSuccessSettings);
        saveToStorage();
      })
      .catch(error => {
        console.error(error);
        toast.showToast("Unknown error. Try again or contact us.", defaultErrorSettings);
      });

};

const deleteArrival = (arrivalId) => {
  const index = arrivals.value.findIndex(arrival => arrival.id === arrivalId);
  if (index !== -1) {
    arrivals.value[index] = { ...defaultArrivals[index] };
    saveToStorage();
  }
  selectedArrivalId.value = null;
};

/*
const hideArrival = (arrivalId) => {
  const index = arrivals.value.findIndex(arrival => arrival.id === arrivalId);
  if (index !== -1) {
    arrivals.value[index].hidden = true;
    saveToStorage()
  }
};

const showArrival = (arrivalId) => {
  const index = arrivals.value.findIndex(arrival => arrival.id === arrivalId);
  if (index !== -1) {
    arrivals.value[index].hidden = false;
    saveToStorage()
  }
};
*/


const handleFileUpload = (event, id) => {
  const file = event.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = (e) => {
    const index = arrivals.value.findIndex(arrival => arrival.id === id);
    if (index === -1) return;

    const img = new Image();
    img.src = e.target.result;
    img.onload = () => {
      const canvas = document.createElement("canvas");
      const ctx = canvas.getContext("2d");

      const [width, height] = arrivals.value[index].imgSize.split("x").map(Number);
      canvas.width = width;
      canvas.height = height;

      ctx.drawImage(img, 0, 0, width, height);
      arrivals.value[index].image = canvas.toDataURL("image/jpeg", 1);
      saveToStorage();
    };
  };
  reader.readAsDataURL(file);
};

onMounted(fetchArrivals);

watch(arrivals, saveToStorage, { deep: true });
</script>
<template>
  <default-container>
    <div class="my-14 space-y-8">
      <section class="container mx-auto">
        <h1 class="font-semibold text-4xl mb-4">New Arrivals Management</h1>
        <div class="flex flex-col items-start mt-12 space-y-8">
          <h2 class="text-xl font-semibold">Arrivals</h2>
          <form class="max-w-max mx-4">
            <label for="arrivalsId" class="block mb-2 text-m font-semibold">Select an option</label>
            <select v-model="selectedArrivalId" id="arrivalsId"
                    class="bg-gray-50 border text-gray-900 text-sm rounded-lg p-2.5">
              <option selected disabled>Choose arr-position</option>
              <option v-for="arrival in arrivals" :key="arrival.id" :value="arrival.id">
                {{ arrival.id + 1 }}
              </option>
            </select>
            <div v-if="selectedArrival">
              <label class="block mt-4 mb-2 text-medium font-medium">Upload file</label>
              <input type="file"
                     class="block text-sm border rounded-lg cursor-pointer"
                     @change="handleFileUpload($event, selectedArrivalId)"
              />


              <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" id="file_input_help">SVG, PNG, JPG or GIF (MAX.
                {{ selectedArrival.imgSize }}).</p>

              <label for="card-title" class="block mt-4 mb-2 text-sm font-medium">Card title</label>
              <div class="flex items-center gap-x-4 flex-wrap">
              <input type="text" id="card-title" v-model="selectedArrival.title"
                     class="bg-gray-50 border text-sm rounded-lg p-2.5"/>
              </div>
              <label for="card-description" class="block mt-4 mb-2 text-sm font-medium">Card description</label>
              <textarea id="card-description" v-model="selectedArrival.description" rows="4"
                        class="block p-2.5 w-full text-sm bg-gray-50 border rounded-lg"></textarea>

              <label for="card-link" class="block mt-4 mb-2 text-sm font-medium">Product link</label>
              <input type="text" id="card-link" v-model="selectedArrival.link"
                     class="bg-gray-50 border text-sm rounded-lg p-2.5"/>

            </div>
          </form>
          <div class="flex gap-4 mt-4">
            <button
                @click="saveArrivals"
                class="bg-blue-500 text-white px-4 py-2 rounded"
            >
              Save
            </button>
            <button v-if="selectedArrival"
                    @click="deleteArrival(selectedArrivalId)"
                    class="bg-red-500 text-white px-3 py-1 rounded"
            >
              Delete
            </button>
            <button v-if="selectedArrival"
                    @click="hideArrival(selectedArrivalId)"
                    class="bg-gray-500 text-white px-3 py-1 rounded"
                    disabled
            >Hide</button>
          </div>
        </div>

      </section>
      <hr/>
      <section class="container mx-auto space-y-4">
        <h2 class="text-xl font-semibold text-center">Arrivals Preview</h2>
        <div class="grid grid-cols-2 gap-12">
          <div class="relative">
            <h2 class="text-l font-semibold text-center">Position-1</h2>
            <img class="w-full" :src="arrivals[0].image" alt="First arrival"/>
            <Description :description="arrivals[0].description" :name="arrivals[0].title"/>
          </div>
          <div class="grid grid-cols-2 grid-rows-2 gap-6 ">
            <div class="col-span-2 h-full relative">
              <h2 class="text-l font-semibold text-center">Position-2</h2>
              <img :src="arrivals[1].image" alt="Second arrival"/>
              <Description :description="arrivals[1].description" :name="arrivals[1].title"/>
            </div>
            <div class="h-full relative">
              <h2 class="text-l font-semibold text-center">Position-3</h2>
              <img :src="arrivals[2].image" alt="Third arrival"/>
              <Description :description="arrivals[2].description" :name="arrivals[2].title"/>
            </div>
            <div class="h-full relative">
              <h2 class="text-l font-semibold text-center">Position-4</h2>
              <img :src="arrivals[3].image" alt="Fourth arrival"/>
              <Description :description="arrivals[3].description" :name="arrivals[3].title"/>
            </div>
          </div>
        </div>
      </section>
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
