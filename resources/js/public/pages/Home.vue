<template>
  <div class="container">
    <!-- Hero Section -->
    <section class="text-center py-5 my-5">
      <h1 class="display-4 fw-bold mb-3">How can we help you today?</h1>
      <p class="fs-5 text-muted mb-4">Find answers, tutorials, and tips to get the most out of our service.</p>
      
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form @submit.prevent="performSearch" class="main-search-bar position-relative">
            <i class="bi bi-search search-icon"></i>
            <input 
              v-model="searchQuery"
              type="search" 
              class="form-control" 
              placeholder="Search for articles, topics, or keywords..."
            >
          </form>
        </div>
      </div>
    </section>

    <!-- Main Content Grid -->
    <div class="row g-5">
      <!-- Categories -->
      <div class="col-lg-8">
        <h2 class="fw-bold mb-4">Browse by Category</h2>
        <div class="row g-4">
          <div v-for="category in categories" :key="category.slug" class="col-md-6">
            <router-link :to="{ name: 'category', params: { slug: category.slug } }" class="text-decoration-none">
              <div class="card h-100 shadow-sm border-0 category-card">
                <div class="card-body p-4 d-flex">
                  <i :class="[category.icon, 'display-6 me-4 text-primary']"></i>
                  <div>
                    <h5 class="card-title fw-bold text-dark mb-1">{{ category.name }}</h5>
                    <p class="card-text text-muted mb-0">{{ category.description }}</p>
                  </div>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Popular Articles -->
      <div class="col-lg-4">
        <div class="bg-white p-4 rounded-3 shadow-sm border">
          <h4 class="fw-bold mb-4">Popular Articles</h4>
          <ul class="list-unstyled mb-0">
            <li v-for="article in popularArticles" :key="article.slug" class="mb-3">
              <router-link :to="{ name: 'article', params: { slug: article.slug } }" class="text-decoration-none d-flex align-items-center popular-article-link">
                <i :class="[article.icon, 'fs-5 text-secondary me-3']"></i>
                <span class="fw-medium text-dark">{{ article.title }}</span>
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const searchQuery = ref('');
const categories = ref([]);
const popularArticles = ref([]);
const router = useRouter();

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'search', query: { q: searchQuery.value } });
  }
};

onMounted(async () => {
  try {
    const response = await axios.get('/api/public/home');
    categories.value = response.data.categories;
    popularArticles.value = response.data.popularArticles;
  } catch (error) {
    console.error('Failed to fetch home page data:', error);
  }
});
</script>

<style scoped>
.category-card {
  transition: all 0.2s ease-in-out;
  border-radius: 0.75rem; /* 12px */
}
.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
}
.category-card .card-title {
  font-size: 1.1rem;
}

.popular-article-link {
  transition: all 0.2s ease-in-out;
}
.popular-article-link:hover {
  opacity: 0.7;
}
.popular-article-link span {
  font-size: 0.95rem;
}
</style>