import { createRouter, createWebHistory } from 'vue-router';

// We will define components in App.vue, but lazy-load the pages here
// for better performance and code-splitting.
const Home = () => import('./pages/Home.vue');
const Category = () => import('./pages/Category.vue');
const Article = () => import('./pages/Article.vue');
const Search = () => import('./pages/Search.vue');

const routes = [
    { 
        path: '/', 
        name: 'home', 
        component: Home 
    },
    { 
        path: '/categories/:slug', 
        name: 'category', 
        component: Category, 
        props: true 
    },
    { 
        path: '/articles/:slug', 
        name: 'article', 
        component: Article, 
        props: true 
    },
    { 
        path: '/search', 
        name: 'search', 
        component: Search, 
        props: (route) => ({ query: route.query.q }) 
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    // This will automatically add Bootstrap's 'active' class to active router links
    linkActiveClass: 'active',
    // Scroll to top on page change
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition;
        } else {
            return { top: 0 };
        }
    },
});

export default router;