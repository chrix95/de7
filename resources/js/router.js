import VueRouter from 'vue-router'
import store from "./store";

// Import Pages
import Home from './pages/Home'
import Login from './pages/Login'
import Logout from './pages/Logout'
import Register from './pages/Register'
import NotFound from './pages/NotFound'
// Customer Pages
import Dashboard from './pages/user/Dashboard'
import CategoryGrid from './pages/user/CategoryGrid'
import Cart from './pages/user/Cart'
import Checkout from './pages/user/Checkout'
// Driver Pages
import DriverDashboard from './pages/driver/Dashboard'
// Admin Pages
import AdminDashboard from './pages/admin/Dashboard'
import AdminInventory from './pages/admin/Inventory'
import AdminOrders from './pages/admin/Orders'
import AdminOrder from './pages/admin/Order'

// Routes
const routes = [
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: {
            requiresAuth: undefined,
            role: null
        }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: {
            requiresAuth: false,
            role: null
        }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            requiresAuth: false,
            role: null
        }
    },
    // CUSTOMER ROUTES
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: {
            requiresAuth: true,
            role: 0
        }
    },
    {
        path: '/view/cart',
        name: 'cart',
        component: Cart,
        meta: {
            requiresAuth: true,
            role: 0
        }
    },
    {
        path: '/checkout',
        name: 'checkout',
        component: Checkout,
        meta: {
            requiresAuth: true,
            role: 0
        }
    },
    {
        path: '/view/category/:catId',
        name: 'category.view',
        component: CategoryGrid,
        props: true,
        meta: {
            requiresAuth: true,
            role: 0
        }
    },
    // DRIVER ROUTES
    {
        path: '/dashboard',
        name: 'driver.dashboard',
        component: DriverDashboard,
        meta: {
            requiresAuth: true,
            role: 1
        }
    },
    // ADMIN ROUTES
    {
        path: '/admin',
        name: 'admin.dashboard',
        component: AdminDashboard,
        meta: {
            requiresAuth: true,
            role: 2
        }
    },
    {
        path: '/admin/inventory',
        name: 'admin.inventory',
        component: AdminInventory,
        meta: {
            requiresAuth: true,
            role: 2
        }
    },
    {
        path: '/admin/orders',
        name: 'admin.orders',
        component: AdminOrders,
        meta: {
            requiresAuth: true,
            role: 2
        }
    },
    {
        path: '/admin/orders/:orderRef',
        name: 'admin.order',
        component: AdminOrder,
        props: true,
        meta: {
            requiresAuth: true,
            role: 2
        }
    },
    {
        path: '/logout',
        name: 'logout',
        component: Logout,
        meta: {
            requiresAuth: false,
            role: null
        }
    },
    {
        path: '*',
        name: 'not.found',
        component: NotFound,
        meta: {
            requiresAuth: false,
            role: null
        }
    }
]
const router = new VueRouter({
    history: true,
    mode: 'history',
    routes,
    store
})

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth) && store.state.isUserLoggedIn == null) {
        next("/login");
    } else {
        if (to.matched.some(record => record.meta.role == store.state.userRole) || to.matched.some(record => record.meta.role == null)) {
            next();
        } else {
            router.push({
                name: store.getters.dashboard
            });
        }
    }
});

export default router