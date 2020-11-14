<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Checkout</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-7">
                        <form @keyup.prevent="enableCheckBtn()">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" :disabled="orderCompleted" v-model="payload.name">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Email</label>
                                        <input type="text" class="form-control" :disabled="orderCompleted" v-model="payload.email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Phone</label>
                                        <input type="text" class="form-control" :disabled="orderCompleted" v-model="payload.phone">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Delivery Address</label>
                                        <textarea v-model="payload.address" class="form-control" :disabled="orderCompleted" cols="30" rows="5"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5">
                        <div class="alert alert-info" v-if="hasError">
                            {{ error_message }}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr v-for="(item, index) in cart" :key="index">
                                        <td scope="row">
                                            <span class="header">{{ item.name }}</span> <br>
                                            <span class="sub-header-left">Qty: {{ item.quantity }}   Price: {{ item.price }}</span>
                                            <span class="sub-header-right">Total: NGN {{ $globalFunc.currency(item.quantity * item.price) }}</span>
                                        </td>
                                    </tr>
                                    <hr>
                                    <tr>
                                        <td scope="row">
                                            <span class="total-left">
                                                Grand Total:
                                            </span>
                                            <span class="total-right">
                                                NGN {{ $globalFunc.currency($store.getters.cartTotal) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <hr>
                                    <tr>
                                        <td scope="row">
                                            <button class="btn btn-md btn-warning btn-block" @click.prevent="verifyTransaction()" :disabled="!checkout || loading || orderCompleted">{{ !loading ? 'Checkout' : 'Please wait...' }}</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import PaymentService from "../../services/PaymentService"
import { mapState } from "vuex";
export default {
    name: "Checkout",
    computed: {
        ...mapState(["loading", "cart", "user"])
    },
    mounted() {
        if (this.$store.getters.cartCount == 0) {
            this.$router.go(-1)
        }
        this.payload.name = this.user.name
        this.payload.email = this.user.email
        this.payload.phone = this.user.phone
        this.payload.cart = this.cart
        this.payload.total_sum = this.$store.getters.cartTotal
    },
    data() {
        return {
            payload: {
                name: null,
                phone: null,
                email: null
            },
            checkout: false,
            error_message: '',
            hasError: false,
            orderCompleted: false
        };
    },
    methods: {
        enableCheckBtn() {
            if(this.payload.name) {
                if(this.payload.email) {
                    if(this.payload.phone) {
                        if (this.payload.address) {
                            this.checkout = true
                        } else {
                            this.checkout = false
                        }
                    } else {
                        this.checkout = false
                    }
                } else {
                    this.checkout = false
                }
            } else {
                this.checkout = false
            }
        },
        payWithCard() {
            this.$store.dispatch("loading", true);
            var that = this;
            var handler = PaystackPop.setup({
                key: process.env.MIX_PAYSTACK_TEST_PUB,
                email: this.user.email,
                amount: this.$store.getters.cartTotal * 100,
                metadata: {
                custom_fields: [
                    {
                        customer_phone: this.user.phone,
                        customer_email: this.user.email
                    },
                ],
                },
                callback: function(response) {
                    if (response.status == "success" && response.message == "Approved") {
                        that.payload.payment_reference = response.reference ? response.reference : response.trxref
                        that.verifyTransaction();
                    } else {
                        that.hasError = true
                        that.error_message = 'Payment unsuccessful. Please try again...';
                    }
                    that.$store.dispatch("loading", false);
                },
                onClose: function() {
                    that.$store.dispatch("loading", false);
                },
            });
            handler.openIframe();
        },
        verifyTransaction() {
            this.$store.dispatch("loading", true);
            PaymentService.verifyPayment(this.payload)
                .then((result) => {
                    if (result.data.status) {
                        this.hasError = true
                        this.orderCompleted = true
                        this.error_message = result.data.message
                    } else {
                        this.hasError = true
                        this.error_message = result.data.message
                    }
                    this.$store.dispatch("loading", false);
                })
                .catch((err) => {
                    this.$store.dispatch("loading", false);
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
                });
        }
    }
};
</script>
<style scoped>
th, td {
    text-align: center;
}
.header {
    font-weight: bold;
    font-size: 22px;
}

.sub-header-left, .sub-header-right {
    font-weight: bold;
    font-size: 14px;
}
.sub-header-left, .total-left {
    float: left;
}
.sub-header-right, .total-right {
    float: right;
}
.total-left, .total-right {
    font-weight: bold;
    font-size: 20px;
}
hr {
    margin-top: 0.1rem;
    margin-bottom: 0.1rem;
}
</style>
