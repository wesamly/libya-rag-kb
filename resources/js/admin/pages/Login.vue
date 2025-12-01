<template>
  <div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background-color: #0d1117;">
    <div class="login-card p-5 rounded-3" style="background-color: #161b22; border: 1px solid #30363d; width: 420px;">
      <div class="text-center mb-4">
        <h2 class="fw-bold">Knowledge Base Admin</h2>
        <p class="text-muted">Sign In to Your Account</p>
      </div>
      
      <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>

      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="email" v-model="form.email" placeholder="Enter your email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" class="form-control" id="password" v-model="form.password" placeholder="Enter your password" required>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="remember" v-model="form.remember">
            <label class="form-check-label" for="remember">Remember Me</label>
          </div>
          <a href="#" class="small text-decoration-none">Forgot Password?</a>
        </div>
        <button type="submit" class="btn btn-primary w-100 btn-lg" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          <span v-else>Login</span>
        </button>
      </form>
      
      <p class="text-center text-muted small mt-5 mb-0">&copy; 2025 KnowledgeBase Inc. All rights reserved.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, inject } from 'vue';
import { useRouter } from 'vue-router';

const auth = inject('$auth');
const router = useRouter();

const form = reactive({
  email: '',
  password: '',
  remember: false,
});
const loading = ref(false);
const errorMessage = ref('');

const handleLogin = async () => {
  loading.value = true;
  errorMessage.value = '';
  try {
    await auth.login(form);
    router.push({ name: 'admin.dashboard' });
  } catch (error) {
    console.error(error);
    if (error.response && error.response.status === 422) {
      errorMessage.value = error.response.data.errors.email[0];
    } else {
      errorMessage.value = 'An unexpected error occurred. Please try again.';
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.form-control {
  padding: 0.75rem 1rem;
}
</style>