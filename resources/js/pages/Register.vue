<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Register</div>
            <div class="card-body">
                <div class="alert alert-danger" v-if="has_error">
                    <p>
                        {{ error_message }}
                    </p>
                </div>
                <form
                    autocomplete="off"
                    @submit.prevent="register()"
                    method="post"
                >
                    <div
                        class="form-group"
                        v-bind:class="{
                            'has-error': has_error
                        }"
                    >
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            class="form-control"
                            placeholder="user@example.com"
                            v-model="payload.email"
                        />
                    </div>
                    <div
                        class="form-group"
                        v-bind:class="{
                            'has-error': has_error
                        }"
                    >
                        <label for="phone">Phone</label>
                        <input
                            type="text"
                            id="phone"
                            class="form-control"
                            v-model="payload.phone"
                        />
                    </div>
                    <div
                        class="form-group"
                        v-bind:class="{
                            'has-error': has_error
                        }"
                    >
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            class="form-control"
                            v-model="payload.password"
                        />
                    </div>
                    <div
                        class="form-group"
                        v-bind:class="{
                            'has-error': has_error
                        }"
                    >
                        <label for="password_confirmation"
                            >Password confirmation</label
                        >
                        <input
                            type="password"
                            id="password_confirmation"
                            class="form-control"
                            v-model="payload.password_confirmation"
                        />
                    </div>
                    <button type="submit" class="btn btn-default">
                        {{ !loading ? 'Register' : 'Please wait...'}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
import AuthenticationService from "../services/AuthenticationService"
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState(["loading", "isUserLoggedIn"])
    },
    created() {
        if (this.isUserLoggedIn) {
            this.$router.push({
                name: this.$store.getters.dashboard,
            })
        }
    },
    data() {
        return {
            payload: {
                name: "",
                email: "",
                phone: "",
                password: "",
                password_confirmation: ""
            },
            has_error: false,
            error_message: null
        };
    },
    methods: {
        register() {
            if (this.validateRegister()) {
                if (navigator.onLine) {
                    this.$store.dispatch("loading", true)
                    AuthenticationService.register(this.payload)
                        .then((result) => {
                            if (result.data.status) {
                                this.loginUser({ email: this.payload.email, password: this.payload.password })
                            } else {
                                this.has_error = true
                                this.error_message = result.data.message
                            }
                            this.$store.dispatch("loading", false)
                        })
                        .catch((err) => {
                            this.$store.dispatch("loading", false)
                            if (err.response === undefined) {
                                this.respondAlert("Oops! took long to get a response", 'error', 'Server Error')
                            } else {
                                this.respondAlert(err.response.data.message, 'error', err.response.statusText)
                            }
                        })
                } else {
                    this.has_error = true
                    this.error_message = 'Check you internet connection and reload'
                }
            }
        },
        validateLogin() {
            if (this.payload.name) {
                if (this.payload.phone && this.payload.phone.length === 11) {
                    if (this.payload.password) {
                        if (this.payload.password_confirmation === this.payload.password) {
                            return true
                        } else {
                            this.has_error = true
                            this.error_message = 'Confirm password doesn\'t match'
                        }
                    } else {
                        this.has_error = true
                        this.error_message = 'Password is required'
                    }
                } else {
                    this.has_error = true
                    this.error_message = 'Phone number is invalid'
                }
            } else {
                this.has_error = true
                this.error_message = 'Email address is required'
            }
        },
        loginUser(payload) {
            AuthenticationService.login(payload)
                .then((result) => {
                    if (result.data.status) {
                        this.$store.dispatch("login", result.data.data)
                        this.$router.push({
                            name: this.$store.getters.dashboard
                        })
                    } else {
                        this.has_error = true
                        this.error_message = result.data.message
                    }
                    this.$store.dispatch("loading", false)
                })
                .catch((err) => {
                    this.$store.dispatch("loading", false)
                    if (err.response === undefined) {
                        this.has_error = true
                        this.error_message = "Oops! took long to get a response"
                    } else {
                        this.has_error = true
                        this.error_message = err.response.data.message
                    }
                })
        }
    }
};
</script>
