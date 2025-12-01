// Import Bootstrap's SCSS
import 'bootstrap/scss/bootstrap.scss';

import './bootstrap'; // Laravel's bootstrap (for axios)
import { createApp } from 'vue';
import App from './admin/App.vue';
import router from './admin/router';
import auth from './admin/store/auth';

// Import and initialize Bootstrap's JS functionality
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// Initialize the auth store
auth.init();

const app = createApp(App);

// Make auth available to all components via inject('$auth')
app.provide('$auth', auth); 

// Make auth global so we can use it anywhere
app.config.globalProperties.$auth = auth;

app.use(router);
app.mount('#admin-app');