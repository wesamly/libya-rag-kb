<template>
  <header class="d-flex align-items-center p-3 px-4 border-bottom" style="background-color: #161b22;">
    <!-- Search Bar -->
    <div class="w-50">
      <form class="position-relative">
        <input type="search" class="form-control" placeholder="Search..." aria-label="Search" style="padding-left: 2.5rem;">
        <i class="bi bi-search position-absolute top-50 translate-middle-y" style="left: 1rem; color: #8b949e;"></i>
      </form>
    </div>

    <!-- Right Icons & User -->
    <div class="ms-auto d-flex align-items-center gap-3">
      <button class="btn btn-link nav-link p-0 fs-5" style="color: #8b949e;">
        <i class="bi bi-bell-fill"></i>
      </button>
      <button class="btn btn-link nav-link p-0 fs-5" style="color: #8b949e;">
        <i class="bi bi-gear-fill"></i>
      </button>

      <div class="dropdown">
        <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <img v-if="auth.user" :src="`https://ui-avatars.com/api/?name=${auth.user.name}&background=0d6efd&color=fff&rounded=true`" alt="mdo" width="32" height="32" class="rounded-circle">
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" style="background-color: #161b22;">
          <li><a class="dropdown-item" href="#">Profile</a></li>
          <li><a class="dropdown-item" href="#">Settings</a></li>
          <li><hr class="dropdown-divider" style="border-color: #30363d;"></li>
          <li><a class="dropdown-item" @click.prevent="auth.logout" href="#">Sign out</a></li>
        </ul>
      </div>
    </div>
  </header>
</template>

<script setup>
import { inject } from 'vue';
const auth = inject('$auth');
</script>