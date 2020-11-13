import VueRouter from 'vue-router'

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
// Driver Pages
import DriverDashboard from './pages/driver/Dashboard'
// Admin Pages
import AdminDashboard from './pages/admin/Dashboard'
import AdminInventory from './pages/admin/Inventory'

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
        path: '/logout',
        name: 'logout',
        component: Logout,
        meta: {
            requiresAuth: false
        }
    },
    {
        path: '*',
        name: 'not.found',
        component: NotFound,
        meta: {
            requiresAuth: false
        }
    }
]
const router = new VueRouter({
    history: true,
    mode: 'history',
    routes,
})
export default router