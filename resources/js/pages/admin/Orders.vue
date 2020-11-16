<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header"><strong>Order Management</strong></div>
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
                                    <tr v-for="(item, index) in orders" :key="index">
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.email }}</td>
                                        <td>{{ item.phone }}</td>
                                        <td>{{ item.order_reference }}</td>
                                        <td>{{ item.amount_paid }}</td>
                                        <td v-if="item.status == 'pending'">
                                            <span class="alert alert-info">New Order</span>
                                        </td>
                                        <td v-else-if="item.status == 'intransit'">
                                            <span class="alert alert-warning">In-transit</span>
                                        </td>
                                        <td v-else-if="item.status == 'delivered'">
                                            <span class="alert alert-success">Delivered</span>
                                        </td>
                                        <td v-else>
                                            <span class="alert alert-danger">Cancelled</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-info btn-sm" @click.prevent="viewOrder(item.order_reference)">View</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12" v-else>
                        <h4 class="text-center">{{ loading ? 'Loading...' : 'No record found' }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import OrderService from "../../services/OrderService"
import { mapState } from 'vuex';
export default {
    name: 'Orders',
    components: {},
    computed: {
        ...mapState(['loading'])
    },
    mounted() {
        this.getOrders()
    },
    data() {
        return {
            orders: []
        }
    },
    methods: {
        getOrders() {
            this.$store.dispatch("loading", true)
            OrderService.getOrders()
                .then((result) => {
                    if (result.data.status) {
                        this.orders = result.data.data
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
                        this.has_error_create = true
                        this.error_message = "Oops! took long to get a response"
                    } else {
                        this.has_error_create = true
                        this.error_message = err.response.data.message
                    }
                })
        },
        viewOrder(ref) {
            this.$router.push({
                name: 'admin.order',
                params: { orderRef: ref }
            })
        }
    }
};
</script>
<style scoped>
.alert {
    padding: 0.2rem 0.25rem;
}
</style>