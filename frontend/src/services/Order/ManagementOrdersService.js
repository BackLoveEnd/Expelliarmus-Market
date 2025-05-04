import managerApi from "@/utils/managerApi.js";

export const ManagementOrdersService = {
    async getOrders(url) {
        return await managerApi().get(url);
    },

    async getOrderLineForOrder(orderId) {
        return await managerApi().get(`/orders/${orderId}/lines`);
    }
};