<template>
  <div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h1 class="h2 fw-bold mb-1">Unanswered Questions</h1>
        <p class="text-muted">Review questions from users where the AI could not find a relevant answer.</p>
      </div>
      <button class="btn btn-secondary" @click="exportCSV"><i class="bi bi-download me-1"></i> Export to CSV</button>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
      <div class="card-body d-flex gap-3 align-items-center">
        <select class="form-select" style="width: 180px;" v-model="filters.date_range" @change="fetchData(1)">
          <option value="7d">Last 7 Days</option>
          <option value="30d">Last 30 Days</option>
          <option value="quarter">This Quarter</option>
          <option value="custom">Custom Range</option>
        </select>

        <div v-if="filters.date_range === 'custom'" class="d-flex gap-2">
            <input type="date" class="form-control" v-model="filters.start_date" @change="fetchData(1)">
            <input type="date" class="form-control" v-model="filters.end_date" @change="fetchData(1)">
        </div>
      </div>
    </div>

    <!-- Content Gap Table -->
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
              <th scope="col">User Question</th>
              <th scope="col">Count</th>
              <th scope="col">Last Asked</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="gap in gaps" :key="gap.user_question">
              <td class="fw-semibold">{{ gap.user_question }}</td>
              <td class="text-muted">{{ gap.count }}</td>
              <td class="text-muted">{{ formatRelativeTime(gap.last_asked) }}</td>
              <td>
                <router-link 
                  :to="{ name: 'admin.articles.new', query: { title: gap.user_question } }"
                  class="btn btn-sm btn-link text-decoration-none"
                >
                  Create Article
                </router-link>
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
              <a class="page-link" href="#" @click.prevent="fetchData(pagination.current_page - 1)">Previous</a>
            </li>
            <li class="page-item" :class="{ disabled: !pagination.next_page_url }">
              <a class="page-link" href="#" @click.prevent="fetchData(pagination.current_page + 1)">Next</a>
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

const gaps = ref([]);
const loading = ref(true);
const pagination = ref({});
const filters = ref({
  date_range: '30d',
  start_date: '',
  end_date: ''
});

const fetchData = async (page = 1) => {
  loading.value = true;
  try {
    const params = { page, ...filters.value };
    const response = await axios.get('/api/admin/content-gap', { params });
    gaps.value = response.data.data;
    pagination.value = response.data;
  } catch (error) {
    console.error("Failed to fetch content gap data:", error);
  } finally {
    loading.value = false;
  }
};

const exportCSV = async () => {
    try {
        const params = { ...filters.value };
        const response = await axios.get('/api/admin/content-gap/export', { 
            params,
            responseType: 'blob'
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'content_gap_export.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error("Failed to export CSV:", error);
    }
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

onMounted(fetchData);
</script>