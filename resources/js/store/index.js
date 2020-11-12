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
    },
    mutations: {
        SET_USER_DATA(state, credentials) {
            state.token = credentials.token
            state.user = credentials.user
            state.userRole = credentials.user.role
            sessionStorage.setItem("setResponse", JSON.stringify(credentials))
            state.isUserLoggedIn = true
            // axios.defaults.headers.common["Authorization"] = `Bearer ${credentials.data.token}`
        },
        RELOAD_USER_DATA(state, credentials) {
            state.token = credentials.token
            state.user = credentials.user
            state.userRole = credentials.user.role
            state.isUserLoggedIn = true
            // axios.defaults.headers.common["Authorization"] = `Bearer ${credentials.token}`
        },
        CLEAR_USER_DATA(state) {
            state.token = null
            state.user = null
            state.userRole = null
            state.isUserLoggedIn = false
            // delete axios.defaults.headers.common["Authorization"];
            sessionStorage.removeItem("setResponse")
        },
        SET_LOADING_STATE(state, value) {
            state.loading = value
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
        }
    }
});