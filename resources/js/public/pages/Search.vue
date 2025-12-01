<template>
  <div class="container mt-5">
    <div class="row g-5">
      <!-- Filters -->
      <div class="col-lg-3">
        <h6 class="text-uppercase text-muted small fw-bold mb-3">Filter by Category</h6>
        <ul class="nav nav-pills flex-column category-nav mb-4">
          <li v-for="cat in categories" :key="cat.slug" class="nav-item">
            <a 
              href="#" 
              @click.prevent="filterByCategory(cat.slug)"
              class="nav-link d-flex justify-content-between align-items-center"
              :class="{ active: cat.slug === activeCategory }"
            >
              <span>{{ cat.name }}</span>
              <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">{{ cat.count }}</span>
            </a>
          </li>
        </ul>

        <h6 class="text-uppercase text-muted small fw-bold mb-3 mt-5">Filter by Date</h6>
        <ul class="nav nav-pills flex-column category-nav">
          <li class="nav-item"><a href="#" class="nav-link">Any time</a></li>
          <li class="nav-item"><a href="#" class="nav-link">Past 24 hours</a></li>
          <li class="nav-item"><a href="#" class="nav-link">Past week</a></li>
          <li class="nav-item"><a href="#" class="nav-link">Past month</a></li>
        </ul>
      </div>

      <!-- Search Results -->
      <div class="col-lg-9">
        <h2 class="fw-bold mb-3">Search results for: '{{ searchQuery }}'</h2>
        <p class="text-muted">Showing {{ results.data.length }} of {{ count }} results</p>

        <div class="search-results-list">
          <div v-for="result in results.data" :key="result.slug" class="card mb-4 shadow-sm border-0">
            <div class="row g-0">
              <div class="col-md-8">
                <div class="card-body p-4">
                  <small class="text-muted">{{ result.breadcrumb }}</small>
                  <h4 class="card-title fw-bold mt-1 mb-2">
                    <router-link :to="{ name: 'article', params: { slug: result.slug } }" class="text-decoration-none text-dark stretched-link">
                      {{ result.title }}
                    </router-link>
                  </h4>
                  <p class="card-text text-muted" v-html="result.snippet"></p>
                </div>
              </div>
              <div class="col-md-4 d-none d-md-block">
                <img :src="result.image" class="img-fluid rounded-end h-100" style="object-fit: cover;" alt="">
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <nav v-if="results.last_page > 1" aria-label="Page navigation" class="mt-5 d-flex justify-content-center">
          <ul class="pagination">
            <li class="page-item" :class="{ disabled: results.current_page === 1 }">
              <a class="page-link" href="#" @click.prevent="fetchPage(results.current_page - 1)">&laquo;</a>
            </li>
            <li 
              v-for="page in results.last_page" 
              :key="page" 
              class="page-item" 
              :class="{ active: page === results.current_page }"
            >
              <a class="page-link" href="#" @click.prevent="fetchPage(page)">{{ page }}</a>
            </li>
            <li class="page-item" :class="{ disabled: results.current_page === results.last_page }">
              <a class="page-link" href="#" @click.prevent="fetchPage(results.current_page + 1)">&raquo;</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();

const searchQuery = ref(route.query.q || '');
const count = ref(0);
const results = ref({ data: [], current_page: 1, last_page: 1 });
const categories = ref([]);
const activeCategory = ref('all');
const isLoading = ref(false);

const fetchData = async (query, page = 1, category = 'all') => {
  if (isLoading.value) return;
  isLoading.value = true;
  try {
    const response = await axios.get('/api/public/search', {
      params: { q: query, page: page, category: category }
    });
    const data = response.data;
    searchQuery.value = data.query;
    count.value = data.count;
    results.value = data.results;
    categories.value = data.categories;
  } catch (error) {
    console.error('Failed to fetch search results:', error);
  } finally {
    isLoading.value = false;
  }
};

const fetchPage = (page) => {
  if (page < 1 || page > results.value.last_page) return;
  fetchData(searchQuery.value, page, activeCategory.value);
};

const filterByCategory = (slug) => {
  activeCategory.value = slug;
  // Update router query
  router.push({ query: { ...route.query, category: slug } });
  fetchData(searchQuery.value, 1, activeCategory.value);
};

onMounted(() => {
  activeCategory.value = route.query.category || 'all';
  fetchData(searchQuery.value, 1, activeCategory.value);
});

// Watch for changes in the route query (e.g., new search from header)
watch(() => route.query.q, (newQuery) => {
  if (newQuery) {
    fetchData(newQuery, 1, activeCategory.value);
  }
});
</script>

<style scoped>
.category-nav .nav-link {
  font-size: 0.95rem;
  font-weight: 500;
  color: #495057;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
  transition: all 0.2s ease;
}
.category-nav .nav-link:hover {
  background-color: #f1f3f5;
}
.category-nav .nav-link.active {
  background-color: #e7f1ff;
  color: #0d6efd;
  font-weight: 600;
}
.card {
  border-radius: 0.75rem;
  overflow: hidden;
  transition: all 0.2s ease-in-out;
}
.card:hover {
  box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
}
.card-body .stretched-link:hover {
  color: #0d6efd !important;
}
</style>