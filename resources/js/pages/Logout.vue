<template>
    <div class="container">
    </div>
</template>
<script>
import AuthenticationService from "../services/AuthenticationService"
export default {
    name: 'Logout',
    mounted() {
        if (this.$store.getters.cartCount != 0) {
            if (confirm(`You have ${this.$store.getters.cartCount} item(s) in your cart. Do you want to proceed?`)) {
                this.logout()
            } else {
                this.$router.go(-1)
            }
        } else {
            this.logout()
        }
    },
    methods: {
        logout() {
            AuthenticationService.logout()
                .then((result) => {
                    if (result.data.status) {
                        this.$store.dispatch("logout");
                        this.$router.push({
                            name: "login"
                        });
                    } else {
                        console.log(result.data)
                    }
                })
                .catch((err) => {
                    console.log(err)
                })
        }
    }
}
</script>
<style scoped>
    
</style>
