<template>
  <nav class="d-flex flex-column flex-shrink-0 p-3" style="width: 280px; background-color: #161b22; border-right: 1px solid #30363d;">
    <router-link to="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none gap-2">
      <i class="bi bi-book-half fs-4"></i>
      <span class="fs-5 fw-semibold">Knowledge Base</span>
    </router-link>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <router-link :to="{ name: 'admin.dashboard' }" class="nav-link text-white" aria-current="page">
          <i class="bi bi-grid-1x2-fill me-2"></i>
          Dashboard
        </router-link>
      </li>
      <li>
        <router-link :to="{ name: 'admin.articles' }" class="nav-link text-white">
          <i class="bi bi-file-text-fill me-2"></i>
          Articles
        </router-link>
      </li>
      <li>
        <router-link :to="{ name: 'admin.categories' }" class="nav-link text-white">
          <i class="bi bi-tags-fill me-2"></i>
          Categories
        </router-link>
      </li>
      <li>
        <router-link :to="{ name: 'admin.analytics' }" class="nav-link text-white">
          <i class="bi bi-bar-chart-fill me-2"></i>
          Analytics
        </router-link>
      </li>
       <li>
        <router-link :to="{ name: 'admin.logs' }" class="nav-link text-white">
          <i class="bi bi-journal-text me-2"></i>
          AI Logs
        </router-link>
      </li>
    </ul>
    <hr>
    <ul class="nav nav-pills flex-column">
      <li>
        <a @click.prevent="doLogout" href="#" class="nav-link text-white">
          <i class="bi bi-box-arrow-left me-2"></i>
          Logout
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { inject } from 'vue';

const auth = inject('$auth');
const doLogout = () => {
  if (confirm('Are you sure you want to log out?')) {
    auth.logout();
  }
};
</script>

<style scoped>
.nav-link {
  color: #c9d1d9 !important;
  font-weight: 500;
}
.nav-link:hover, .nav-link.active {
  background-color: #21262d;
  color: #c9d1d9 !important;
}
.nav-link i {
  color: #8b949e;
}
.nav-link.active i {
  color: #c9d1d9;
}
</style>