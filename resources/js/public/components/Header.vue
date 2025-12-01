<template>
  <header class="bg-white border-bottom shadow-sm">
    <nav class="navbar navbar-expand-lg">
      <div class="container py-2">
        <!-- Logo -->
        <router-link to="/" class="navbar-brand d-flex align-items-center gap-2">
          <i class="bi bi-book-half fs-4 text-primary"></i>
          <span class="fs-4 fw-bold text-dark">KnowledgeBase</span>
        </router-link>

        <!-- Search Bar (for larger screens) -->
        <div class="d-none d-lg-block w-50 mx-auto">
          <form @submit.prevent="performSearch" class="position-relative">
            <input 
              v-model="searchQuery"
              class="form-control" 
              type="search" 
              placeholder="Search for articles..." 
              aria-label="Search">
            <i class="bi bi-search position-absolute top-50 translate-middle-y" style="left: 1rem; color: #6c757d;"></i>
          </form>
        </div>

        <!-- Right Side Nav -->
        <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-3">
          <!-- Nav Links (hidden on small screens, shown on lg) -->
          <li class="nav-item d-none d-lg-block">
            <router-link to="/categories/getting-started" class="nav-link">Categories</router-link>
          </li>
          <li class="nav-item d-none d-lg-block">
            <a href="#" class="nav-link">Contact</a>
          </li>

          <!-- Buttons -->
          <li class="nav-item">
            <a href="#" class="btn btn-outline-secondary">Log In</a>
          </li>
          <li class="nav-item">
            <a href="#" class="btn btn-primary d-none d-md-block">Sign Up</a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const searchQuery = ref('');
const router = useRouter();

const performSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({ name: 'search', query: { q: searchQuery.value } });
    searchQuery.value = ''; // Clear search bar
  }
};
</script>

<style scoped>
.navbar-brand .bi {
  margin-top: -5px;
}
.navbar .form-control {
  border-radius: 0.5rem;
  padding-left: 2.75rem;
}
.navbar .btn {
  border-radius: 0.5rem;
  font-weight: 500;
}
</style>