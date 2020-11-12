// import axios from 'axios'
import './bootstrap'
import Vue from 'vue'
import VueRouter from 'vue-router'
import Index from './Index'
import router from './router'
import store from "./store";


// Set Vue globally
window.Vue = Vue

// Set Vue router
Vue.router = router
Vue.use(VueRouter)

// Set Vue authentication
// axios.defaults.baseURL = `${process.env.MIX_APP_URL}/api`

// Load Index
Vue.component('index', Index)

const app = new Vue({
  el: '#app',
  router,
  store,
  created() {
    const userString = sessionStorage.getItem("setResponse");
    if (userString) {
      const userData = JSON.parse(userString);
      this.$store.dispatch("reloadUserData", userData);
    }
    axios.interceptors.response.use(
      response => response,
      error => {
        if (error.response.status === 401) {
          this.$store.dispatch("logout");
        }
        return Promise.reject(error);
      }
    );
  },
});

export default app