import axios from "axios";
import {useAuthStore} from "@/stores/useAuthStore.js";
import {useRouter} from "vue-router";

export default function api() {
    const router = useRouter();

    const api = axios.create({
        baseURL: `/api`,
        withCredentials: true,
        withXSRFToken: true,
        xsrfCookieName: "XSRF-TOKEN",
        xsrfHeaderName: "X-XSRF-TOKEN",
        headers: {
            "Content-Type": "application/json",
        },
    });

    api.interceptors.request.use(async (config) => {
        if (config.method !== "get" && !config.headers["X-XSRF-TOKEN"]) {
            await axios.get(`/api/sanctum/csrf-cookie`, {
                withCredentials: true,
            });
        }

        return config;
    });

    api.interceptors.response.use(
        function (response) {
            return response;
        },
        function (error) {
            const authStore = useAuthStore();

            if (error.response?.status === 503) {
                router.push({path: '/maintenance'});

                return Promise.reject(error);
            }

            if (error.response?.status >= 500 && error.response?.status <= 599) {
                router.push({path: '/500', state: {redirected: true}});

                return Promise.reject(error);
            }

            if (error.request?.status === 403) {
            }

            if ([401, 419].includes(error.response?.status)) {
                if (authStore.isManager || authStore.isSuperManager) {
                    return Promise.reject(error);
                }

                authStore.forgetUser();
            }

            return Promise.reject(error);
        },
    );

    return api;
}
