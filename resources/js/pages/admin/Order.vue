<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">
                <strong>Order Management</strong>
            </div>
            <div class="card-body">
                <div class="row" v-if="!loading && order.order_reference">
                    <div class="col-md-12">
                        <hr>
                            <h6>
                                <strong>ORDER INFORMATION</strong>
                            </h6>
                        <hr>
                    </div>
                    <div class="col-md-12" v-if="hasError">
                        <div class="alert alert-info">
                            {{ error_message }}
                        </div>
                    </div>
                    <div class="col-md-12 mbt-15" style="float: right;">
                        <label for="status">Update status</label>
                        <select v-model="order.status" style="width: 20%" class="form-control" :disabled="order.status == 'delivered'" @change.prevent="updateStatus()">
                            <option value="pending" :selected="order.status == 'pending' ? 'selected' : '' ">New</option>
                            <option value="intransit" :selected="order.status == 'intransit' ? 'selected' : '' ">In-transit</option>
                            <option value="delivered" :selected="order.status == 'delivered' ? 'selected' : '' ">Delivered</option>
                            <option value="cancelled" :selected="order.status == 'cancelled' ? 'selected' : '' ">Canncelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="name"><strong>NAME</strong></label>
                            <input type="text" name="name" class="form-control" :value="order.name" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email"><strong>EMAIL</strong></label>
                            <input type="text" name="email" class="form-control" :value="order.email" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="phone"><strong>PHONE</strong></label>
                            <input type="text" name="phone" class="form-control" :value="order.phone" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="order_reference"><strong>ORDER REFERENCE</strong></label>
                            <input type="text" name="order_reference" class="form-control" :value="order.order_reference" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address"><strong>DELIVERY ADDRESS</strong></label>
                            <input type="text" name="address" class="form-control" :value="order.address" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="address"><strong>TOTAL AMOUNT</strong></label>
                            <input type="text" name="address" class="form-control" :value="$globalFunc.currency(order.amount)" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="payment_reference"><strong>PAYMENT REFERENCE</strong></label>
                            <input type="text" name="payment_reference" class="form-control" :value="order.payment_reference" disabled>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status"><strong>ORDER STATUS</strong></label>
                            <div class="alert alert-info" v-if="order.status == 'pending'">NEW</div>
                            <div class="alert alert-warning" v-else-if="order.status == 'intransit'">In-transit</div>
                            <div class="alert alert-success" v-else-if="order.status == 'delivered'">Delivered</div>
                            <div class="alert alert-danger" v-else>Cancelled</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr>
                        <h6><strong>ORDER CONTENT</strong></h6>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in order.order_content" :key="index">
                                        <th scope="row">{{ index + 1 }}</th>
                                        <td>{{ item.product_name }}</td>
                                        <td>
                                            {{ item.product_quantity }}
                                        </td>
                                        <td>NGN {{ $globalFunc.currency(item.product_price) }}</td>
                                        <td>NGN {{ $globalFunc.currency(item.product_price * item.product_quantity) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-right">
                                            <strong>Total Amount:</strong>
                                        </td>
                                        <td colspan="2"  class="text-left">
                                            <strong>NGN {{ $globalFunc.currency(order.amount) }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row" v-else>
                    <div class="col-md-12">
                        <h4 class="text-center">{{ loading ? 'Loading...' : error_message }}</h4>
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
    name: 'Order',
    components: {},
    props: ['orderRef'],
    computed: {
        ...mapState(['loading'])
    },
    mounted() {
        this.getOrder()
    },
    data() {
        return {
            order: {},
            hasError: false,
            error_message: ''
        }
    },
    methods: {
        getOrder() {
            this.$store.dispatch("loading", true)
            OrderService.getOrder(this.orderRef)
                .then((result) => {
                    if (result.data.status) {
                        this.order = result.data.data
                        this.hasError = false
                    } else {
                        this.hasError = true
                        this.error_message = result.data.message
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
        updateStatus() {
            OrderService.saveStatus({ orderRef: this.order.order_reference, status: this.order.status})
                .then((result) => {
                    if (result.data.status) {
                        alert(`Order status has been update`)
                        this.hasError = false
                    } else {
                        this.hasError = true
                        this.error_message = result.data.message
                    }
                })
                .catch((err) => {
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
        }
    }
};
</script>
<style scoped>
.alert {
    padding: 0.2rem 0.25rem;
}
</style>