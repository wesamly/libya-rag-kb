<template>
  <div class="d-flex flex-column min-vh-100">
    <!-- Header -->
    <Header />

    <!-- Main Content Area -->
    <main class="flex-grow-1">
      <!-- RouterView will render the current page (Home, Category, etc.) -->
      <router-view />
    </main>
    
    <!-- Footer -->
    <Footer />

    <!-- Chatbox Component -->
    <Chatbox />
  </div>
</template>

<script setup>
import Header from './components/Header.vue';
import Footer from './components/Footer.vue';
import Chatbox from './components/Chatbox.vue';
</script>

<style>
/* Global styles can go here if needed */
.main-search-bar .form-control {
  padding: 1rem 1.5rem;
  padding-left: 3.5rem;
  font-size: 1.1rem;
  border-radius: 0.75rem; /* 12px */
  border-color: #dee2e6;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
}

.main-search-bar .search-icon {
  position: absolute;
  left: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  font-size: 1.25rem;
}
</style>