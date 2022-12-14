require('./bootstrap');

import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap";
import "vue-select/dist/vue-select.css";

import * as Vue from 'vue'

import { library } from '@fortawesome/fontawesome-svg-core';
import { faCircleUser } from '@fortawesome/free-solid-svg-icons';
library.add(faCircleUser);
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

import { createPinia } from "pinia";

import axios from 'axios';
import VueAxios from 'vue-axios';

import vSelect from "vue-select";

import App from './components/App.vue';

if (document.getElementById('app')) {
    const app = Vue.createApp({});
    const pinia = createPinia();

    app.component('app', App);
    app.component('FontAwesomeIcon', FontAwesomeIcon);
    app.component('v-select', vSelect);
    app.use(pinia);
    app.use(VueAxios, axios);
    app.provide('axios', app.config.globalProperties.axios);
    app.mount('#app');
}