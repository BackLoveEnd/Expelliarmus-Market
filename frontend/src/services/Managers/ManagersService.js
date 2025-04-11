import {useJsonApiFormatter} from "@/composables/useJsonApiFormatter.js";
import managerApi from "@/utils/managerApi.js";

const formatter = useJsonApiFormatter();

export const ManagersService = {
    async getManagers(params) {
        const url = formatter.formatUrl('/managers', params);

        return managerApi().get(url);
    }
};