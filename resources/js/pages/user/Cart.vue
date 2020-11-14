<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Cart Item(s)</div>
            <div class="card-body">
                <div class="row"  v-if="$store.getters.cartCount > 0">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Total</th>
                                        <th scope="col">[Action]</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in cart" :key="index">
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ item.name }}</td>
                                        <td>
                                            <input type="number" v-model="item.quantity" @change.prevent="$store.dispatch('updateCart', { id: item.id, qty: item.quantity })">
                                        </td>
                                        <td>NGN {{ $globalFunc.currency(item.price) }}</td>
                                        <td>NGN {{ $globalFunc.currency(item.price * item.quantity) }}</td>
                                        <td>
                                            <span @click.prevent="$store.dispatch('removeCart', item.id)" style="color: red; cursor: pointer">X</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right">
                                            <strong>Total Amount:</strong>
                                        </td>
                                        <td colspan="2"  class="text-left">
                                            <strong>NGN {{ $globalFunc.currency($store.getters.cartTotal) }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 text-right">
                        <router-link :to="{ name: 'checkout' }">
                            <button class="btn btn-md btn-warning" :disabled="$store.getters.cartTotal == 0">
                                Proceed to Checkout
                            </button>
                        </router-link>
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col-md-12 text-center">
                        <h4>No item in cart</h4>
                        <router-link :to="{ name: 'dashboard' }" class="btn btn-sm btn-warning">Go to shop</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from "vuex";
export default {
    name: "Cart",
    computed: {
        ...mapState(["loading", "cart"])
    },
    mounted() {},
    data() {
        return {
        };
    },
    methods: {}
};
</script>
<style scoped>
th, td {
    text-align: center;
}
</style>
