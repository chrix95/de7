<template>
    <div class="container">
        <div class="card card-default">
            <div class="card-header">Login</div>
            <div class="card-body">
                <div class="alert alert-danger" v-if="has_error">
                    <p class="error_message">
                        {{ error_message }}
                    </p>
                </div>
                <form autocomplete="off" @submit.prevent="login()" method="post">
                    <div class="form-group">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            class="form-control"
                            placeholder="user@example.com"
                            v-model="user.email"
                            required
                        />
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            class="form-control"
                            v-model="user.password"
                            required
                        />
                    </div>
                    <button type="submit" class="btn btn-default" :disabled="loading">
                        {{ loading ? 'Please wait...' : 'Login' }}
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
                name: "dashboard",
            })
        }
    },
    data() {
        return {
            user: {
                email: null,
                password: null,
            },
            has_error: false,
            error_message: null
        };
    },
    mounted() {
        //
    },
    methods: {
        login() {
            if (this.validateLogin()) {
                this.$store.dispatch("loading", true)
                AuthenticationService.login(this.user)
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
        },
        validateLogin() {
            if (this.user.email) {
                if (this.user.password) {
                    return true
                } else {
                    this.has_error = true
                    this.error_message = 'Password is required'
                }
            } else {
                this.has_error = true
                this.error_message = 'Email address is required'
            }
        }
    }
};
</script>
