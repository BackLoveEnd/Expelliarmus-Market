import api from "@/utils/api.js";
import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";

const formatter = useJsonApiFormatter();

export const RegularCustomersService = {
    async getUsers(params) {
        const url = formatter.formatUrl('/management/users/regular-customers', params);

        return await api().get(url);
    },

    async getGuests(params) {
        const url = formatter.formatUrl(`/management/users/guests`, params);

        return await api().get(url);
    }
};