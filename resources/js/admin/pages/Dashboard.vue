<template>
  <div>
    <h1 class="h2 fw-bold mb-4">RAG System Overview</h1>

    <!-- Stat Cards -->
    <div class="row g-4">
      <div class="col-md-3">
        <div class="card h-100">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="card-title text-muted fw-medium mb-0">Articles to Sync</h6>
              <button @click="runSync" class="btn btn-link p-0 text-primary" :disabled="syncing" title="Run Sync">
                <i class="bi bi-arrow-clockwise fs-5"></i>
              </button>
            </div>
            <h2 class="fw-bold mb-0" :class="{ 'text-warning': stats.articles_to_sync > 0 }">
              {{ stats.articles_to_sync }}
            </h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100">
          <div class="card-body">
            <h6 class="card-title text-muted fw-medium mb-3">Total Published</h6>
            <h2 class="fw-bold mb-0">{{ stats.total_published }}</h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100">
          <div class="card-body">
            <h6 class="card-title text-muted fw-medium mb-3">Chat Queries (24h)</h6>
            <h2 class="fw-bold mb-0">{{ stats.chat_queries_24h }}</h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card h-100">
          <div class="card-body">
            <h6 class="card-title text-muted fw-medium mb-3">Negative Feedback (24h)</h6>
            <h2 class="fw-bold mb-0" :class="{ 'text-danger': stats.negative_feedback_24h > 0 }">
              {{ stats.negative_feedback_24h }}
            </h2>
          </div>
        </div>
      </div>
    </div>

    <!-- RAG Pipeline Status -->
    <div class="card mt-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">RAG Pipeline Status</h5>
        <span class="text-muted small">Last updated: {{ pipeline.last_sync_relative }}</span>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <i class="bi bi-arrow-repeat text-primary fs-5 me-2"></i>
            <strong>Last Successful Sync</strong>
            <p class="mb-0 text-muted">{{ pipeline.last_sync }}</p>
          </div>
          <div class="col-md-4">
            <i class="bi bi-database-check text-success fs-5 me-2"></i>
            <strong>Vector DB Status</strong>
            <p class="mb-0 text-muted">{{ pipeline.vector_db_status }}</p>
          </div>
          <div class="col-md-4">
            <i class="bi bi-bounding-box-circles text-info fs-5 me-2"></i>
            <strong>Total Chunks</strong>
            <p class="mb-0 text-muted">{{ pipeline.total_chunks?.toLocaleString() }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Chat Activity -->
    <div class="card mt-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Recent Chat Activity</h5>
        <router-link :to="{ name: 'admin.logs' }" class="btn btn-sm btn-link text-decoration-none">View All</router-link>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th scope="col">User</th>
              <th scope="col">Question</th>
              <th scope="col">AI Response</th>
              <th scope="col">Feedback</th>
              <th scope="col">Timestamp</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="activity in recentActivity" :key="activity.id">
              <td>{{ activity.user }}</td>
              <td>{{ truncate(activity.user_question, 30) }}</td>
              <td>{{ truncate(activity.ai_response, 40) }}</td>
              <td>
                <i v-if="activity.user_feedback === 'good'" class="bi bi-hand-thumbs-up-fill text-success"></i>
                <i v-if="activity.user_feedback === 'bad'" class="bi bi-hand-thumbs-down-fill text-danger"></i>
                <span v-if="activity.user_feedback === 'none'" class="text-muted">-</span>
              </td>
              <td class="text-muted">{{ formatRelativeTime(activity.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const stats = ref({});
const pipeline = ref({});
const recentActivity = ref([]);
const syncing = ref(false);

const fetchData = async () => {
  try {
    const response = await axios.get('/api/admin/stats');
    stats.value = response.data;
    pipeline.value = response.data.pipeline_status;
    // Format dates
    pipeline.value.last_sync_relative = formatRelativeTime(pipeline.value.last_sync);
  } catch (error) {
    console.error("Failed to fetch dashboard stats:", error);
  }
  
  try {
    const response = await axios.get('/api/admin/recent-activity');
    recentActivity.value = response.data;
  } catch (error) {
    console.error("Failed to fetch recent activity:", error);
  }
};

const runSync = async () => {
  if (syncing.value) return;
  if (!confirm('This will run the Python ingestion script. This may take a few moments. Continue?')) {
    return;
  }
  syncing.value = true;
  try {
    await axios.post('/api/admin/run-sync');
    alert('Sync process started successfully. Refresh the page in a moment to see updates.');
    await fetchData(); // Refresh data
  } catch (error) {
    console.error("Sync failed:", error);
    alert('Error starting sync: ' + (error.response?.data?.error || 'Unknown error.'));
  } finally {
    syncing.value = false;
  }
};

const truncate = (text, length) => {
  return text.length > length ? text.substring(0, length) + '...' : text;
};

const formatRelativeTime = (date) => {
  if (!date) return 'N/A';
  // This is a simple relative time formatter, you might use a library like date-fns
  const seconds = Math.floor((new Date() - new Date(date)) / 1000);
  let interval = seconds / 31536000;
  if (interval > 1) return Math.floor(interval) + " years ago";
  interval = seconds / 2592000;
  if (interval > 1) return Math.floor(interval) + " months ago";
  interval = seconds / 86400;
  if (interval > 1) return Math.floor(interval) + " days ago";
  interval = seconds / 3600;
  if (interval > 1) return Math.floor(interval) + " hours ago";
  interval = seconds / 60;
  if (interval > 1) return Math.floor(interval) + " minutes ago";
  return Math.floor(seconds) + " seconds ago";
};

onMounted(fetchData);
</script>

<style scoped>
.card {
  border-radius: 0.5rem;
}
.table-hover tbody tr:hover {
  background-color: var(--bs-table-hover-bg);
  color: var(--bs-table-hover-color);
}
</style>