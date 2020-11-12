<template>
    <div class="container">
    </div>
</template>
<script>
import AuthenticationService from "../services/AuthenticationService"
export default {
    name: 'Logout',
    mounted() {
        this.logout()
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
