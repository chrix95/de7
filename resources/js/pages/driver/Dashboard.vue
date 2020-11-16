<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Driver Dashboard</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12" v-if="!loading && orders.length > 0">
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Order Reference</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">[Action]</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, index) in orders"
                                        :key="index"
                                    >
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.email }}</td>
                                        <td>{{ item.phone }}</td>
                                        <td>{{ item.order_reference }}</td>
                                        <td>{{ item.amount_paid }}</td>
                                        <td v-if="item.status == 'intransit'">
                                            <span class="alert alert-warning"
                                                >In-transit</span
                                            >
                                        </td>
                                        <td v-if="item.status == 'delivered'">
                                            <span class="alert alert-success"
                                                >Delivered</span
                                            >
                                        </td>
                                        <td v-if="item.status !== 'delivered'">
                                            <button
                                                class="btn btn-info btn-sm"
                                                @click.prevent="
                                                    markDelivered(
                                                        item.order_reference
                                                    )
                                                "
                                            >
                                                Delivered
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12" v-else>
                        <h4 class="text-center">
                            {{ loading ? "Loading..." : "No record found" }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from 'vuex';
import OrderService from "../../services/OrderService";
export default {
    data() {
        return {
            orders: [],
            hasError: false,
            error_message: ''
        };
    },
    computed: {
        ...mapState(['loading'])
    },
    mounted() {
        this.getOrders()
    },
    methods: {
        getOrders() {
            this.$store.dispatch("loading", true)
            OrderService.driverOrders()
                .then((result) => {
                    if (result.data.status) {
                        this.orders = result.data.data
                        this.hasError = false
                    }
                    this.$store.dispatch("loading", false)
                })
                .catch((err) => {
                    this.$store.dispatch("loading", false)
                    if (err.response.status === 401) {
                        this.$store.dispatch("logout")
                        this.$router.push({
                            name: 'login'
                        });
                    }
                    if (err.response === undefined) {
                        this.hasError = true
                        this.error_message = "Oops! took long to get a response"
                    } else {
                        this.hasError = true
                        this.error_message = err.response.data.message
                    }
                })
        },
        markDelivered(ref) {
            this.$store.dispatch("loading", true)
            OrderService.updateDelivered({ orderRef: ref })
                .then((result) => {
                    if (result.data.status) {
                        this.getOrders()
                    }
                    this.$store.dispatch("loading", false)
                })
                .catch((err) => {
                    this.$store.dispatch("loading", false)
                    if (err.response.status === 401) {
                        this.$store.dispatch("logout")
                        this.$router.push({
                            name: 'login'
                        });
                    }
                    if (err.response === undefined) {
                        this.hasError = true
                        this.error_message = "Oops! took long to get a response"
                    } else {
                        this.hasError = true
                        this.error_message = err.response.data.message
                    }
                })
        },
    }
};
</script>
