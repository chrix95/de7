<template>
    <nav id="nav">
        <ul v-if="!isUserLoggedIn">
            <li class="menu">
                <router-link :to="{ name: 'home' }" class="mr-15">Home</router-link>
                <router-link :to="{ name: 'login' }" class="mr-15">Login</router-link>
                <router-link :to="{ name: 'register' }" class="mr-15">Register</router-link>
            </li>
        </ul>
        <ul v-else>
            <li class="menu" v-for="(item, id) in routes" :key="id">
                <router-link :to="{ name: item.name }" class="mr-15">
                    {{ item.description }}
                </router-link>
            </li>
        </ul>
    </nav>
</template>
<script>
import { mapState } from "vuex";
export default {
    computed: {
        ...mapState(['isUserLoggedIn', 'userRole']),
        routes() {
            return this.menu.filter(c => c.role === this.userRole || c.role === null)
        }
    },
    data() {
        return {
            menu: [
                {
                    name: 'home',
                    description: 'Home',
                    role: null
                },
                // CUSTOMER ROUTES
                {
                    name: 'dashboard',
                    description: 'Dashboard',
                    role: 0
                },
                // DRIVER ROUTES
                {
                    name: 'driver.dashboard',
                    description: 'Dashboard',
                    role: 1
                },
                // ADMIN ROUTES
                {
                    name: 'admin.dashboard',
                    description: 'Dashboard',
                    role: 2
                },
                {
                    name: 'admin.inventory',
                    description: 'Inventory Managament',
                    role: 2
                },
                {
                    name: 'admin.orders',
                    description: 'Order Managament',
                    role: 2
                },
                {
                    name: 'logout',
                    description: 'Logout',
                    role: null
                },
            ]
        };
    },
    mounted() {
        //
    },
    methods: {
        dashboard() {
            this.$router.push({
                name: this.$store.getters.dashboard
            })
        },
        logout() {

        }
    }
};
</script>
<style scoped>
nav#nav ul {
    list-style-type: none;
    text-align: center;
}
#nav ul li.menu {
    display: inline-block;
}
#nav ul li.menu a.router-link-active {
    font-size: 15px !important;
    font-weight: bold;
    margin: auto 20px;
}
</style>