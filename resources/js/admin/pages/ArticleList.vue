<template>
  <div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h2 fw-bold mb-0">Articles</h1>
      <router-link :to="{ name: 'admin.articles.new' }" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Write New Article
      </router-link>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body d-flex gap-3">
        <input 
          type="search" 
          class="form-control" 
          placeholder="Search articles..." 
          v-model="filters.search"
          @keydown.enter.prevent="applyFilters"
        >
        
        <select class="form-select" style="width: 200px;" v-model="filters.category">
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat.value" :value="cat.value">{{ cat.label }}</option>
        </select>
        
        <select class="form-select" style="width: 150px;" v-model="filters.status">
          <option value="">All Statuses</option>
          <option value="published">Published</option>
          <option value="draft">Draft</option>
          <option value="archived">Archived</option>
        </select>
        
        <select class="form-select" style="width: 150px;" v-model="filters.sync">
          <option value="">Sync Status</option>
          <option value="1">Pending</option>
          <option value="0">Synced</option>
        </select>

        <button class="btn btn-secondary" @click="applyFilters">Filter</button>
      </div>
    </div>

    <!-- Article Table -->
    <div class="card">
      <div v-if="loading" class="text-center p-5">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <div v-else class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th scope="col">Title</th>
              <th scope="col">Status</th>
              <th scope="col">Category</th>
              <th scope="col">Sync Status</th>
              <th scope="col">Last Updated</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="article in articles" :key="article.id">
              <td>
                <router-link 
                  :to="{ name: 'admin.articles.edit', params: { id: article.id } }"
                  class="fw-semibold text-decoration-none"
                >
                  {{ article.title }}
                </router-link>
              </td>
              <td>
                <span :class="['badge', statusClass(article.status)]">{{ article.status }}</span>
              </td>
              <td>{{ article.category?.name || 'N/A' }}</td>
              <td>
                <span v-if="article.needs_sync" class="badge bg-warning"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                <span v-else class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> Synced</span>
              </td>
              <td class="text-muted">{{ formatRelativeTime(article.updated_at) }}</td>
              <td>
                <button @click="deleteArticle(article.id)" class="btn btn-link text-danger p-0" title="Delete">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Footer with Pagination -->
      <div v-if="!loading && pagination.last_page > 1" class="card-footer d-flex justify-content-between align-items-center">
        <span class="text-muted small">
          Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} results
        </span>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item" :class="{ disabled: !pagination.prev_page_url }">
              <a class="page-link" href="#" @click.prevent="fetchData(pagination.current_page - 1)">&laquo;</a>
            </li>
            <!-- ... Pagination numbers ... -->
            <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
              <a class="page-link" href="#" @click.prevent="fetchData(pagination.current_page + 1)">&raquo;</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const articles = ref([]);
const categories = ref([]);
const loading = ref(true);
const pagination = ref({});
const filters = ref({
  search: '',
  category: '',
  status: '',
  sync: '',
});

const fetchData = async (page = 1) => {
  loading.value = true;
  try {
    const params = { page, ...filters.value };
    const response = await axios.get('/api/admin/articles', { params });
    articles.value = response.data.data;
    pagination.value = response.data;
  } catch (error) {
    console.error("Failed to fetch articles:", error);
  } finally {
    loading.value = false;
  }
};

const fetchCategories = async () => {
  try {
    // Re-use the editor data endpoint
    const response = await axios.get('/api/admin/articles/editor-data');
    categories.value = response.data.categories;
  } catch (error) {
    console.error("Failed to fetch categories:", error);
  }
};

const applyFilters = () => {
  fetchData(1); // Reset to page 1
};

const deleteArticle = async (id) => {
  if (confirm('Are you sure you want to delete this article? This will also remove its vector data.')) {
    try {
      await axios.delete(`/api/admin/articles/${id}`);
      fetchData(pagination.value.current_page); // Refresh
    } catch (error) {
      console.error("Failed to delete article:", error);
      alert('Error deleting article.');
    }
  }
};

const statusClass = (status) => {
  if (status === 'published') return 'bg-success';
  if (status === 'draft') return 'bg-secondary';
  if (status === 'archived') return 'bg-danger';
  return 'bg-secondary';
};

const formatRelativeTime = (date) => {
  if (!date) return 'N/A';
  const seconds = Math.floor((new Date() - new Date(date)) / 1000);
  let interval = seconds / 86400;
  if (interval > 1) return Math.floor(interval) + " days ago";
  interval = seconds / 3600;
  if (interval > 1) return Math.floor(interval) + " hours ago";
  interval = seconds / 60;
  if (interval > 1) return Math.floor(interval) + " minutes ago";
  return "Just now";
};

onMounted(() => {
  fetchData();
  fetchCategories();
});
</script>