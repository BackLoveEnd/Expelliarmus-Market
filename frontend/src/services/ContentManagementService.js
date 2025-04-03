import api from "@/utils/api.js";

export const ContentManagementService = {
  async getAllSlides() {
    return await api().get("/management/content/slider");
  },

  async uploadSliderContent(data) {
    const form = new FormData();

    data.forEach((slide, index) => {
      form.append(`images[${index}][image]`, slide.image ?? "");
      form.append(`images[${index}][slide_id]`, slide.slide_id ?? "");
      form.append(`images[${index}][image_url]`, slide.image_url ?? "");
      form.append(`images[${index}][order]`, slide.order);
      form.append(`images[${index}][content_url]`, slide.content_url);
    });

    return await api().post("/management/content/slider", form, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
  },

  async deleteSlide(slideId) {
    return api().delete(`/management/content/slider/slides/${slideId}`);
  },

  async getAllArrivals() {
    return await api().get("/management/content/new-arrivals");
  },


  async uploadArrivalContent(data) {
    function dataURItoFile(dataURI, filename) {
      const arr = dataURI.split(",");
      const mime = arr[0].match(/:(.*?);/)[1];
      const bstr = atob(arr[1]);
      let n = bstr.length;
      const u8arr = new Uint8Array(n);
      while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
      }
      return new File([u8arr], filename, { type: mime });
    }

    const form = new FormData();

    data.forEach((arrival, index) => {
      if (!arrival.exists_image_url || arrival.exists_image_url.trim() === "") {
        arrival.exists_image_url = null;
      }

      const hasValidExistsImageUrl =
          arrival.exists_image_url && typeof arrival.exists_image_url === 'string' && arrival.exists_image_url.startsWith("http://expelliarmus.com:8080/storage/content/arrivals");

      const file = arrival.file && arrival.file.startsWith("data:image")
          ? dataURItoFile(arrival.file, `arrival-${index}.jpg`)
          : null;
      if (!hasValidExistsImageUrl && !file) {
        return;
      }

      if (file) form.append(`arrivals[${index}][file]`, file);
      const content= {
        title: arrival.title,
        description: arrival.description
      }
      form.append(`arrivals[${index}][position]`, arrival.position ?? index + 1);
      form.append(`arrivals[${index}][arrival_id]`, arrival.index ?? null);
      form.append(`arrivals[${index}][exists_image_url]`, hasValidExistsImageUrl ? arrival.exists_image_url : "");
      form.append(`arrivals[${index}][arrival_url]`, arrival.arrival_url ?? "");
      form.append(`arrivals[${index}][content]`, arrival.content ?? "");
    });

    console.log("Sent data (as FormData):", [...form.entries()]);

    return await api().post("/management/content/new-arrivals", form, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
  },

  async deleteArrival(arrivalId) {
    return api().delete(`/management/content/new-arrivals/${arrivalId}`);
  },
};