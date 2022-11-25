require('./bootstrap');

import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";

import * as Vue from 'vue'

import axios from 'axios';
import VueAxios from 'vue-axios';

import App from './components/App.vue';

if (document.getElementById('app')) {
    const app = Vue.createApp({});

    app.component('app', App);
    app.use(VueAxios, axios);
    app.provide('axios', app.config.globalProperties.axios);
    app.mount('#app');
}