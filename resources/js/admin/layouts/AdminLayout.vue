<template>
  <div class="d-flex" style="min-height: 100vh;">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content -->
    <div class="flex-grow-1 d-flex flex-column">
      <!-- Top Navigation -->
      <TopNav />

      <!-- Page Content -->
      <main class="flex-grow-1 p-4" style="background-color: #0d1117;">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import Sidebar from '../components/Sidebar.vue';
import TopNav from '../components/TopNav.vue';
</script>