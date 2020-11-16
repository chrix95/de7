import Api from "./Api";

export default {
    getOrders() {
        return Api().get("auth/orders");
    },
    getOrder(orderRef) {
        return Api().get(`auth/order/${orderRef}`);
    },
    saveStatus(credentials) {
        return Api().put("auth/order/status", credentials);
    }
};
