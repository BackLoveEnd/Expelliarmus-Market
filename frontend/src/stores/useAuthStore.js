import {defineStore} from "pinia";
import api from "@/utils/api.js";
import {login, logout} from "@/utils/auth.js";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: null,
        isSessionVerified: false,
    }),
    getters: {
        isRegularUser: (state) => state?.user?.role === 'regular_user',
        firstName: (state) => state?.user?.first_name,
        lastName: (state) => state?.user?.last_name,
        address: (state) => state?.user?.address,
        email: (state) => state?.user?.email,
        phone_mask: (state) => state?.user?.phone_mask,
        phone_original: (state) => state?.user?.phone_original,
        fullName: (state) => state?.user?.full_name,
        role: (state) => state?.user?.role,
        isManager: (state) => state?.user?.role === "manager",
        isSuperManager: (state) => state?.user?.role === "super_manager",
    },
    actions: {
        async login(user, manager = false) {
            return await login(user, manager);
        },

        async fetchCurrentUser(force = false) {
            if (this.isSessionVerified && !force) {
                this.isSessionVerified = false;
                return;
            }

            try {
                const response = await api().get("/current-user");
                this.user = response?.data?.data.attributes;
                this.isSessionVerified = true;
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

        async logout(manager = false) {
            return await logout(manager).finally(() => {
                this.forgetUser();
            });
        },

        forgetUser() {
            this.user = null;
            this.isSessionVerified = false;
        },
    },
});
