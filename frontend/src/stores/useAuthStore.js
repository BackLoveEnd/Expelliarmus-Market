import { defineStore } from "pinia";
import api from "@/utils/api.js";
import { login, logout } from "@/utils/auth.js";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    isSessionVerified: false,
  }),
  getters: {
    isAuthenticated: (state) => !!state?.user,
    firstName: (state) => state?.user?.first_name,
    lastName: (state) => state?.user?.last_name,
    email: (state) => state?.user?.email,
    fullName: (state) => state?.user?.full_name,
  },
  actions: {
    async login(user) {
      return await login(user);
    },

    async fetchCurrentUser(force = false) {
      if (this.isSessionVerified && !force) {
        this.isSessionVerified = false;
        return;
      }

      try {
        const response = await api().get("/current-user");
        this.user = response?.data?.data.attributes;
      } catch (e) {
        if (e.response?.status === 401 || e.response?.status === 419) {
          this.forgetUser();
        }
      }
    },

    async attempt() {
      try {
        const response = await api().get("/current-user");
        this.user = response?.data?.data.attributes;
      } catch (e) {
        if (e.response?.status === 401 || e.response?.status === 419) {
          this.forgetUser();
        }
      } finally {
        this.isSessionVerified = true;
      }
    },

    async logout() {
      return await logout().finally(() => {
        this.forgetUser();
      });
    },

    forgetUser() {
      this.user = null;
      this.isSessionVerified = false;
    },
  },
});
