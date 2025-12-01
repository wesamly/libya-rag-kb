<template>
  <div>
    <h1 class="h2 fw-bold mb-4">My Profile</h1>
    <div class="card">
      <div class="card-body">
        <form @submit.prevent="updateProfile">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" v-model="user.name" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" v-model="user.email" required>
              </div>
            </div>
          </div>
          <hr>
          <p class="text-muted">Leave password fields blank to keep current password.</p>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" v-model="user.password">
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="password_confirmation" v-model="user.password_confirmation">
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            {{ loading ? 'Saving...' : 'Save Changes' }}
          </button>
          <div v-if="successMessage" class="alert alert-success mt-3" role="alert">
            {{ successMessage }}
          </div>
          <div v-if="errorMessage" class="alert alert-danger mt-3" role="alert">
            {{ errorMessage }}
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const user = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});
const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const fetchUser = async () => {
  try {
    const response = await axios.get('/api/admin/profile');
    user.value = { ...response.data, password: '', password_confirmation: '' };
  } catch (error) {
    console.error('Failed to fetch user data:', error);
    errorMessage.value = 'Failed to load user data.';
  }
};

const updateProfile = async () => {
  loading.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  try {
    const payload = {
      name: user.value.name,
      email: user.value.email,
    };
    if (user.value.password) {
      payload.password = user.value.password;
      payload.password_confirmation = user.value.password_confirmation;
    }
    await axios.put('/api/admin/profile', payload);
    successMessage.value = 'Profile updated successfully!';
    user.value.password = '';
    user.value.password_confirmation = '';
  } catch (error) {
    errorMessage.value = 'Failed to update profile. Please check your input.';
    console.error('Failed to update profile:', error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchUser);
</script>
