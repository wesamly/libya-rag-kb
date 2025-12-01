<template>
  <div class="container mt-5">
    <div class="row g-5">
      <!-- Sidebar -->
      <div class="col-lg-3">
        <h6 class="text-uppercase text-muted small fw-bold mb-3">Categories</h6>
        <ul class="nav nav-pills flex-column category-nav">
          <li v-for="cat in categoryList" :key="cat.slug" class="nav-item">
            <router-link :to="{ name: 'category', params: { slug: cat.slug } }" class="nav-link d-flex align-items-center gap-2">
              <i :class="[cat.icon, 'fs-5']"></i>
              {{ cat.name }}
            </router-link>
          </li>
        </ul>
      </div>

      <!-- Main Content -->
      <div class="col-lg-9">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-2">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><router-link to="/">Home</router-link></li>
            <li class="breadcrumb-item"><router-link to="/">Categories</router-link></li>
            <li class="breadcrumb-item active" aria-current="page">{{ category.name }}</li>
          </ol>
        </nav>

        <h1 class="display-5 fw-bold mb-3">{{ category.name }}</h1>
        <p class="fs-5 text-muted mb-4">{{ category.description }}</p>

        <!-- Article List -->
        <div class="list-group list-group-flush article-list">
          <router-link 
            v-for="article in articles" 
            :key="article.slug"
            :to="{ name: 'article', params: { slug: article.slug } }"
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0 py-4"
          >
            <div class="d-flex align-items-center">
              <i class="bi bi-file-text fs-4 text-primary me-4"></i>
              <div>
                <h5 class="mb-1 fw-bold">{{ article.title }}</h5>
                <p class="mb-0 text-muted">{{ article.description }}</p>
              </div>
            </div>
            <i class="bi bi-chevron-right fs-5 text-muted d-none d-sm-block"></i>
          </router-link>
        </div>

        <!-- Pagination -->
        <nav v-if="pagination.last_page > 1" aria-label="Page navigation" class="mt-5 d-flex justify-content-center">
          <ul class="pagination">
            <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
              <a class="page-link" href="#" @click.prevent="fetchPage(pagination.current_page - 1)">&laquo;</a>
            </li>
            <li 
              v-for="page in pagination.last_page" 
              :key="page" 
              class="page-item" 
              :class="{ active: page === pagination.current_page }"
            >
              <a class="page-link" href="#" @click.prevent="fetchPage(page)">{{ page }}</a>
            </li>
            <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
              <a class="page-link" href="#" @click.prevent="fetchPage(pagination.current_page + 1)">&raquo;</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const category = ref({});
const articles = ref([]);
const categoryList = ref([]);
const pagination = ref({ current_page: 1, last_page: 1 });
const isLoading = ref(false);

const props = defineProps({
  slug: String
});

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/public/categories');
    categoryList.value = response.data;
  } catch (error) {
    console.error('Failed to fetch categories:', error);
  }
};

const fetchCategoryData = async (slug, page = 1) => {
  if (isLoading.value) return;
  isLoading.value = true;
  try {
    const response = await axios.get(`/api/public/category/${slug}?page=${page}`);
    category.value = response.data.category;
    articles.value = response.data.articles.data;
    pagination.value.current_page = response.data.articles.current_page;
    pagination.value.last_page = response.data.articles.last_page;
  } catch (error) {
    console.error('Failed to fetch category data:', error);
  } finally {
    isLoading.value = false;
  }
};

const fetchPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return;
  fetchCategoryData(props.slug, page);
};

// Fetch data when component mounts
onMounted(() => {
  fetchCategories();
  fetchCategoryData(props.slug);
});

// Watch for changes in the route (e.g., clicking another category link)
watch(() => props.slug, (newSlug) => {
  fetchCategoryData(newSlug);
});
</script>

<style scoped>
.category-nav .nav-link {
  font-size: 1rem;
  font-weight: 500;
  color: #495057;
  padding: 0.6rem 1rem;
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
.article-list .list-group-item:first-child {
  border-top: 1px solid #dee2e6;
}
.article-list .list-group-item {
  transition: background-color 0.2s ease-in-out;
}
</style>