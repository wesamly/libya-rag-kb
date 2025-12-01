<template>
  <div class="d-flex" style="height: calc(100vh - 90px);">
    
    <!-- Log List (Left Side) -->
    <div class="d-flex flex-column" style="width: 350px; background-color: #161b22; border-right: 1px solid #30363d;">
      <div class="p-3 border-bottom">
        <h5 class="fw-bold mb-0">AI Interaction Logs</h5>
      </div>
      <div class="p-3 border-bottom">
        <input type="search" class="form-control" placeholder="Search by keyword..." v-model="filters.search" @keydown.enter.prevent="applyFilters">
      </div>
      <div class="flex-grow-1" style="overflow-y: auto;">
        <div class="list-group list-group-flush">
          <a 
            v-for="log in logs" 
            :key="log.id" 
            href="#"
            @click.prevent="selectLog(log)"
            class="list-group-item list-group-item-action py-3"
            :class="{ active: selectedLog && selectedLog.id === log.id }"
          >
            <p class="mb-1 fw-semibold text-truncate">{{ log.user_question }}</p>
            <small class="text-muted">{{ formatTimestamp(log.created_at) }}</small>
          </a>
        </div>
      </div>
      <!-- Pagination -->
      <div class="p-3 border-top d-flex justify-content-between">
        <button class="btn btn-sm btn-outline-secondary" :disabled="!pagination.prev_page_url" @click="fetchData(pagination.current_page - 1)">Previous</button>
        <span class="text-muted small">Page {{ pagination.current_page }}</span>
        <button class="btn btn-sm btn-outline-secondary" :disabled="!pagination.next_page_url" @click="fetchData(pagination.current_page + 1)">Next</button>
      </div>
    </div>

    <!-- Log Details (Right Side) -->
    <div class="flex-grow-1 p-4" style="overflow-y: auto;">
      <div v-if="!selectedLog && !loading" class="d-flex h-100 align-items-center justify-content-center text-muted">
        <p>Select a log to view details</p>
      </div>
      <div v-if="loading" class="d-flex h-100 align-items-center justify-content-center">
        <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <div v-if="selectedLog && !loading">
        <h3 class="fw-bold mb-4">Log Details</h3>

        <div class="mb-4">
          <h6 class="text-muted text-uppercase small fw-bold">User Question</h6>
          <p class="fs-5">{{ selectedLog.user_question }}</p>
        </div>

        <div class="mb-4">
          <h6 class="text-muted text-uppercase small fw-bold">Full AI Response</h6>
          <div class="p-3 rounded" style="background-color: #161b22; border: 1px solid #30363d;">
            <p style="white-space: pre-wrap;">{{ selectedLog.ai_response }}</p>
          </div>
        </div>

        <div class="mb-4">
          <h6 class="text-muted text-uppercase small fw-bold">Retrieved Articles</h6>
          <div v-if="detailedLog.retrieved_articles?.length > 0">
            <router-link 
              v-for="article in detailedLog.retrieved_articles"
              :key="article.id"
              :to="{ name: 'admin.articles.edit', params: { id: article.id } }"
              class="btn btn-sm btn-outline-primary me-2"
            >
              {{ article.title }} (ID: {{ article.id }})
            </router-link>
          </div>
          <p v-else class="text-muted">No articles were retrieved for this response.</p>
        </div>

        <div class="mb-4">
          <h6 class="text-muted text-uppercase small fw-bold">Retrieved Context (from Vector DB)</h6>
          <div class="p-3 rounded" style="background-color: #161b22; border: 1px solid #30363d; font-family: monospace; font-size: 0.85rem;">
            <div v-for="(context, index) in detailedLog.retrieved_context" :key="index" class="mb-2">
              <p class="mb-0"><strong>From Article-{{ context.article_id }}:</strong> {{ context.text }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const logs = ref([]);
const selectedLog = ref(null);
const detailedLog = ref({});
const loading = ref(true);
const loadingDetail = ref(false);
const pagination = ref({});
const filters = ref({ search: '', feedback: 'all' });

const fetchData = async (page = 1) => {
  loading.value = true;
  try {
    const params = { page, ...filters.value };
    const response = await axios.get('/api/admin/chat-history', { params });
    logs.value = response.data.data;
    pagination.value = response.data;
  } catch (error) {
    console.error("Failed to fetch chat logs:", error);
  } finally {
    loading.value = false;
  }
};

const selectLog = async (log) => {
  loadingDetail.value = true;
  selectedLog.value = log; // Show basic info immediately
  try {
    const response = await axios.get(`/api/admin/chat-history/${log.id}`);
    detailedLog.value = response.data;
    // Overwrite selectedLog with full data
    selectedLog.value = response.data.log;
  } catch (error) {
    console.error("Failed to fetch log details:", error);
  } finally {
    loadingDetail.value = false;
  }
};

const applyFilters = () => {
  fetchData(1);
};

const formatTimestamp = (date) => {
  return new Date(date).toLocaleString();
};

onMounted(fetchData);
</script>

<style scoped>
.list-group-item {
  border-bottom: 1px solid var(--bs-list-group-border-color) !important;
}
.list-group-item.active {
  background-color: #0d6efd;
  border-color: #0d6efd;
}
</style>