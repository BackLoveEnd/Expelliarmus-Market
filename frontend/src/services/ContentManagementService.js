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
};
