import api from "@/utils/api.js";

export const StatisticsService = {
    async getStatisticForManagerHome() {
        return await api().get("/management/statistics/general-home");
    }
};