import Api from "./Api";

export default {
    getCategories() {
        return Api().get("auth/categories");
    },
    createCategory(credentials) {
        return Api().post("auth/categories", credentials);
    },
    updateCategory(credentials) {
        return Api().put("auth/categories", credentials);
    },
    deleteCategory(credentials) {
        return Api().delete("auth/categories", { data: credentials });
    }
};
