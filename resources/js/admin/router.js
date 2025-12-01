import { createRouter, createWebHistory } from 'vue-router';
import auth from './store/auth';

// Lazy load all pages
const Login = () => import('./pages/Login.vue');
const AdminLayout = () => import('./layouts/AdminLayout.vue');
const Dashboard = () => import('./pages/Dashboard.vue');
const ArticleList = () => import('./pages/ArticleList.vue');
const ArticleEditor = () => import('./pages/ArticleEditor.vue');
const ChatHistory = () => import('./pages/ChatHistory.vue');
const ContentGap = () => import('./pages/ContentGap.vue');
const CategoryList = () => import('./pages/CategoryList.vue');
const UserProfile = () => import('./pages/UserProfile.vue');

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { requiresGuest: true }
    },
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: '',
                name: 'admin.dashboard',
                component: Dashboard
            },
            {
                path: 'articles',
                name: 'admin.articles',
                component: ArticleList
            },
            {
                path: 'articles/new',
                name: 'admin.articles.new',
                component: ArticleEditor
            },
            {
                path: 'articles/:id/edit',
                name: 'admin.articles.edit',
                component: ArticleEditor,
                props: true
            },
            {
                path: 'categories',
                name: 'admin.categories',
                component: CategoryList
            },
            {
                path: 'profile',
                name: 'admin.profile',
                component: UserProfile
            },
            {
                path: 'analytics',
                name: 'admin.analytics',
                component: ContentGap // This is the "Content Gap" page from your screenshot
            },
            {
                path: 'logs',
                name: 'admin.logs',
                component: ChatHistory // This is the "AI Interaction Logs" page
            },
        ]
    },
    // Add a 404 route later
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    linkActiveClass: 'active',
});

// Navigation Guard
router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !auth.user) {
        // Not logged in, redirect to login
        return next({ name: 'login' });
    }
    if (to.meta.requiresGuest && auth.user) {
        // Already logged in, redirect to dashboard
        return next({ name: 'admin.dashboard' });
    }
    return next();
});

export default router;