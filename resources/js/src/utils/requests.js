import axios from 'axios';
import _ from 'lodash';
// import { getToken, setToken } from '@/utils/auth';
import Token from "@/utils/token";
import Language from "@/utils/language";
import Vue from "vue";
// Create axios instance
const Request = axios.create({
  baseURL: process.env.SELLER_BASE_API,
  timeout: 10000, // Request timeout
});

// Request intercepter
Request.interceptors.request.use(
  config => {
    if (Token.hasToken()) {
      config.headers['Authorization'] = 'Bearer ' + Token.getToken(); // Set JWT token
    }
    config.headers['Language'] = Language.getLanguage(); // Set Language header

    return config;
  },
  error => {
    // Do something with request error
    console.log(error); // for debug
    Promise.reject(error);
  }
);

// response pre-processing
Request.interceptors.response.use(
  response => {
    if (response.headers.authorization) {
      Token.setToken(response.headers.authorization);
      response.data.token = response.headers.authorization;
    }

    return _.get(response, 'data', {});
  },
  error => {
    // Vue.notify({
    //   title: 'Authorization',
    //   text: 'You have been logged in!'
    // })
    // let message = error.message;
    // if (error.response.data && error.response.data.errors) {
    //   message = error.response.data.errors;
    // } else if (error.response.data && error.response.data.error) {
    //   message = error.response.data.error;
    // }

    // return Promise.reject(error);
    let result = {
      error: true,
      message: error.response.data.message,
    }

    if (error.response.status === 401) {
      Token.removeToken()
      Token.removeUser()
      location.reload()
    }

    if (error.response && error.response.status === 422) {
      const errors = error.response.data.errors
      Object.keys(errors).forEach((field) => {
        const errorMessage = errors[field][0]
        result.message = errorMessage
        Vue.$toast.error(errorMessage)
      });
    } else {
      Vue.$toast.error(error.response.data.message)
    }

    if (error.response.data && error.response.data.errors) {
      result.errors = error.response.data.errors
    }

    if (error.response && error.response.status === 422) {

    }

    return result;
  },
);

export default Request;
