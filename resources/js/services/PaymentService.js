import Api from "./Api";

export default {
    verifyPayment(credentials) {
        return Api().post("auth/checkout/verify", credentials);
    }
};
