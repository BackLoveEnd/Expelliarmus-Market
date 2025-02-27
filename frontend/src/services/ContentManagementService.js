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
    return await api().get("/management/content/newarrivals");
  },


  async uploadArrivalContent(data) {
    const form = new FormData();
    data.forEach((arrival, index) => {
      form.append(`arrivals[${index}][file]`, arrival.file ?? "");
      form.append(`arrivals[${index}][position]`, arrival.position);
      form.append(`arrivals[${index}][arrival_id]`, arrival.arrival_id ?? "");
      form.append(`arrivals[${index}][exists_image_url]`, arrival.exists_image_url ?? "");
      form.append(`arrivals[${index}][arrival_url]`, arrival.arrival_url);
      form.append(`arrivals[${index}][content]`, JSON.stringify(arrival.content));
    });

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
