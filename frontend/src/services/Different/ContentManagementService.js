import api from '@/utils/api.js';
import managerApi from "@/utils/managerApi.js";

export const ContentManagementService = {
    async getAllSlides() {
        return await api().get('/management/content/slider');
    },

    async uploadSliderContent(data) {
        const form = new FormData();

        data.forEach((slide, index) => {
            form.append(`images[${index}][image]`, slide.image ?? '');
            form.append(`images[${index}][slide_id]`, slide.slide_id ?? '');
            form.append(`images[${index}][image_url]`, slide.image_url ?? '');
            form.append(`images[${index}][order]`, slide.order);
            form.append(`images[${index}][content_url]`, slide.content_url);
        });

        return await managerApi().post('/content/slider', form, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },

    async deleteSlide(slideId) {
        return managerApi().delete(`/content/slider/slides/${slideId}`);
    },

    async getAllArrivals() {
        return await api().get('/management/content/new-arrivals');
    },

    async uploadArrivalContent(data) {
        console.log(data);

        function dataURItoFile(dataURI, filename) {
            const arr = dataURI.split(',');
            const mime = arr[0].match(/:(.*?);/)[1];
            const bstr = atob(arr[1]);
            let n = bstr.length;
            const u8arr = new Uint8Array(n);

            while (n--) {
                u8arr[n] = bstr.charCodeAt(n);
            }

            return new File([u8arr], filename, {type: mime});
        }

        const form = new FormData();

        data.forEach((arrival, index) => {
            const hasValidExistsImageUrl =
                arrival.image_url &&
                arrival.image_url.startsWith('http://api.expelliarmus.com:8080/storage/content/arrivals');

            const file = arrival.image_url.startsWith('data:image')
                ? dataURItoFile(arrival.image_url, `arrival-${index}.jpg`)
                : null;

            if (!hasValidExistsImageUrl && !file) {
                console.error(`Error: the ${index + 1}st element does not neither file, neither exists_image_url`);
                return;
            }

            if (file) form.append(`arrivals[${index}][file]`, file);

            form.append(`arrivals[${index}][position]`, arrival.position ?? index + 1);

            if (arrival.id) form.append(`arrivals[${index}][arrival_id]`, arrival.id);
            if (hasValidExistsImageUrl) form.append(`arrivals[${index}][exists_image_url]`, arrival.image_url);

            const isValidUrl =
                arrival.arrival_url &&
                (arrival.arrival_url.startsWith('http://expelliarmus.com') || arrival.arrival_url.startsWith('https://expelliarmus.com'));

            if (isValidUrl) {
                form.append(`arrivals[${index}][arrival_url]`, arrival.arrival_url);
            } else {
                console.error(`Error: the ${index}st element not valid arrival_url`);
                return;
            }

            if (arrival.content.title) form.append(`arrivals[${index}][content][title]`, arrival.content.title);
            if (arrival.content.body) form.append(`arrivals[${index}][content][body]`, arrival.content.body);
        });

        return await managerApi().post('/content/new-arrivals', form, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
    },

    async deleteArrivalContent(arrivalId) {
        return managerApi().delete(`/content/new-arrivals/${arrivalId}`);
    },
};
