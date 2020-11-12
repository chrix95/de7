/* eslint-disable no-undef */
import axios from "axios";

// create a new axios instance
const instance = axios.create({
    baseURL: `/api/`
});

// before a request is made start the nprogress
instance.interceptors.request.use(config => {
    const userString = JSON.parse(sessionStorage.getItem("setResponse"));
    if (userString && userString.token) {
        config.headers['Authorization'] = `Bearer ${userString.token}`;
    }
    return config;
});

export default () => instance;
