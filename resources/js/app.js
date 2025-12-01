import './bootstrap';

import { createApp } from 'vue';
import App from './public/App.vue';
import router from './public/router';

// Import Bootstrap's JS functionality
// This is separate from the CDN link in the blade file
import 'bootstrap'; 

const app = createApp(App);

app.use(router);
app.mount('#app');