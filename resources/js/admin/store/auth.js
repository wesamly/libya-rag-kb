import { reactive } from 'vue';
import axios from 'axios';
import router from '../router';

const auth = reactive({
    user: null,
    loading: true,

    /**
     * Fetch the user data from Laravel
     */
    async init() {
        this.loading = true;
        try {
            const response = await axios.get('/api/admin/user');
            this.user = response.data;
        } catch (error) {
            this.user = null;
        } finally {
            this.loading = false;
        }
    },

    /**
     * Handle user login
     */
    async login(credentials) {
        // 1. Get CSRF cookie
        await axios.get('/sanctum/csrf-cookie');

        // 2. Attempt login
        await axios.post('/login', credentials);

        // 3. Fetch the authenticated user
        await this.init();
    },

    /**
     * Handle user logout
     */
    async logout() {
        await axios.post('/logout');
        this.user = null;
        router.push({ name: 'login' });
    }
});

export default auth;