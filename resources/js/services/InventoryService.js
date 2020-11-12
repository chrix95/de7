import Api from "./Api";

export default {
    getInventory() {
        return Api().get("auth/inventories");
    },
    createInventory(credentials) {
        return Api().post("auth/inventories", credentials);
    },
    updateInventory(credentials) {
        return Api().put("auth/inventories", credentials);
    },
    deleteInventory(credentials) {
        return Api().delete("auth/inventories", { data: credentials });
    }
};
