import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        token: null,
        user: null,
        userRole: null,
        isUserLoggedIn: false,
        loading: false,
        cart: []
    },
    mutations: {
        SET_USER_DATA(state, credentials) {
            state.token = credentials.token
            state.user = credentials.user
            state.userRole = credentials.user.role
            sessionStorage.setItem("setResponse", JSON.stringify(credentials))
            state.isUserLoggedIn = true
        },
        RELOAD_USER_DATA(state, credentials) {
            state.token = credentials.token
            state.user = credentials.user
            state.userRole = credentials.user.role
            state.isUserLoggedIn = true
        },
        CLEAR_USER_DATA(state) {
            state.token = null
            state.user = null
            state.userRole = null
            state.isUserLoggedIn = false
            state.cart = []
            sessionStorage.removeItem("setResponse")
            sessionStorage.removeItem("cartItem")
        },
        SET_LOADING_STATE(state, value) {
            state.loading = value
        },
        RELOAD_CART_ITEMS(state, items) {
            state.cart = items
        },
        REMOVE_CART_ITEM(state, itemId) {
            state.cart = state.cart.filter(c => c.id !== itemId)
            sessionStorage.setItem('cartItem', JSON.stringify(state.cart))
        },
        CLEAR_CART_ITEMS(state) {
            state.cart = []
            sessionStorage.removeItem('cartItem')
        },
        UPDATE_CART_ITEM(state, item) {
            let existItem = state.cart.find(c => c.id == item.id)
            existItem.quantity = item.qty
            sessionStorage.setItem('cartItem', JSON.stringify(state.cart))
        },
        SET_CART_ITEM(state, item) {
            var cartItem =  state.cart.find(c => c.id == item.id)
            let newItem = item
            if (cartItem) {
                cartItem.quantity = cartItem.quantity + 1
            } else {
                newItem.quantity = 1
                state.cart = [...state.cart, newItem]
            }
            sessionStorage.setItem('cartItem', JSON.stringify(state.cart))
        }
    },
    actions: {
        login({ commit }, credentials) {
            commit("SET_USER_DATA", credentials)
        },
        reloadUserData({ commit }, credentials) {
            commit("RELOAD_USER_DATA", credentials)
        },
        logout({ commit }) {
            commit("CLEAR_USER_DATA")
        },
        loading({ commit }, value) {
            commit("SET_LOADING_STATE", value)
        },
        addToCart({ commit }, item) {
            commit("SET_CART_ITEM", item)
        },
        updateCart({ commit }, item) {
            commit("UPDATE_CART_ITEM", item)
        },
        removeCart({ commit }, itemId) {
            commit("REMOVE_CART_ITEM", itemId)
        },
        reloadCartItems({ commit }, items) {
            commit("RELOAD_CART_ITEMS", items)
        },
        clearCartItems({ commit }) {
            commit("CLEAR_CART_ITEMS")
        }
    },
    getters: {
        dashboard(state) {
            if (state.userRole == 0) {
                return 'dashboard'
            }
            if (state.userRole == 1) {
                return 'driver.dashboard'
            }
            if (state.userRole == 2) {
                return 'admin.dashboard'
            }
        },
        cartCount(state) {
            return state.cart.length
        },
        cartTotal(state) {
            return state.cart.reduce((acc, item) => acc + Number(item.price) * Number(item.quantity), 0);
        }
    }
});