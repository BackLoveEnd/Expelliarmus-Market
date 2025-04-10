import managerApi from "@/utils/managerApi.js";

export const StatisticsService = {
    async getStatisticForManagerHome() {
        return await managerApi().get("/statistics/general-home");
    }
};