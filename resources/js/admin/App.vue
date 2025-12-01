<template>
  <!-- The router will render either Login or AdminLayout -->
  <router-view />
</template>

<style>
/* Global styles for the admin panel */
:root {
  --bs-body-bg: #0d1117;
  --bs-body-color: #c9d1d9;
  --bs-border-color: #30363d;
  --bs-list-group-bg: #161b22;
  --bs-list-group-border-color: #30363d;
  --bs-card-bg: #161b22;
  --bs-card-border-color: #30363d;
  --bs-modal-bg: #161b22;
  --bs-modal-header-border-color: #30363d;
  --bs-modal-footer-border-color: #30363d;
  --bs-form-control-bg: #0d1117;
  --bs-form-control-border-color: #30363d;
  --bs-form-select-bg: #0d1117;
  --bs-form-select-border-color: #30363d;
  --bs-body-secondary-color: #8b949e;
  --bs-secondary-subtle: #161b22;
  --bs-tertiary-bg: #161b22;
  --bs-primary-rgb: 58, 161, 255;
  --bs-primary: #3a9fff;
  --bs-primary-hover: #58a6ff;
  --bs-btn-color: #c9d1d9;
  --bs-btn-hover-color: #c9d1d9;
  --bs-btn-active-color: #c9d1d9;
}

body {
  background-color: var(--bs-body-bg);
  color: var(--bs-body-color);
}

.form-control, .form-select {
  color: var(--bs-body-color);
}
.form-control:focus, .form-select:focus {
  background-color: var(--bs-form-control-bg);
  color: var(--bs-body-color);
  border-color: #58a6ff;
  box-shadow: 0 0 0 0.25rem rgba(58, 161, 255, 0.25);
}

.form-control::placeholder {
  color: #8b949e;
}

.btn-primary {
  --bs-btn-bg: #238636;
  --bs-btn-border-color: #2ea043;
  --bs-btn-hover-bg: #2ea043;
  --bs-btn-hover-border-color: #3fb950;
  --bs-btn-active-bg: #2ea043;
  --bs-btn-active-border-color: #3fb950;
  font-weight: 500;
}

.btn-secondary {
  --bs-btn-bg: #21262d;
  --bs-btn-border-color: #30363d;
  --bs-btn-hover-bg: #30363d;
  --bs-btn-hover-border-color: #8b949e;
  font-weight: 500;
}

.table {
  --bs-table-bg: #161b22;
  --bs-table-border-color: #30363d;
  --bs-table-striped-bg: #21262d;
  --bs-table-hover-bg: #21262d;
  --bs-table-hover-color: #c9d1d9;
}

.badge.bg-success {
  background-color: rgba(35, 134, 54, 0.2) !important;
  color: #3fb950 !important;
}
.badge.bg-secondary {
  background-color: rgba(139, 148, 158, 0.2) !important;
  color: #8b949e !important;
}
.badge.bg-danger {
  background-color: rgba(248, 81, 73, 0.2) !important;
  color: #f85149 !important;
}
.badge.bg-warning {
    background-color: rgba(234, 179, 8, 0.2) !important;
    color: #eab308 !important;
}
</style>