import axios from "axios";
import { useAuthStore } from "@/stores/useAuthStore.js";

export default function api(isApiCall = true) {
  const api = axios.create({
    baseURL: `http://api.expelliarmus.com:8080${isApiCall ? "/api" : ""}`,
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
      await axios.get("http://api.expelliarmus.com:8080/sanctum/csrf-cookie", {
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
      if (error.request?.status === 403) {
      }

      if ([401, 419].includes(error.request?.status)) {
        useAuthStore().forgetUser();
      }

      return Promise.reject(error);
    },
  );

  return api;
}
