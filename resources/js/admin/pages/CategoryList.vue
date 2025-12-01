<template>
  <div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h2 fw-bold mb-0">Categories</h1>
      <button class="btn btn-primary" @click="openModal()">
        <i class="bi bi-plus-lg me-1"></i> Create New
      </button>
    </div>

    <!-- Category Table -->
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
              <th scope="col">Name</th>
              <th scope="col">Description</th>
              <th scope="col" class="text-center">Articles</th>
              <th scope="col">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in categories" :key="category.id">
              <td>
                <span class="fw-semibold">{{ category.name }}</span>
              </td>
              <td class="text-muted">{{ category.description }}</td>
              <td class="text-center">
                <span class="badge bg-primary rounded-pill">{{ category.articles_count }}</span>
              </td>
              <td>
                <button class="btn btn-link p-0" title="Edit" @click="openModal(category)">
                  <i class="bi bi-pencil-fill"></i>
                </button>
                <button class="btn btn-link text-danger p-0 ms-2" title="Delete" @click="deleteCategory(category.id)">
                  <i class="bi bi-trash-fill"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true" ref="categoryModalRef">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="categoryModalLabel">{{ isEditMode ? 'Edit' : 'Create' }} Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCategory">
              <div class="mb-3">
                <label for="categoryName" class="form-label">Name</label>
                <input type="text" class="form-control" id="categoryName" v-model="currentCategory.name" required>
              </div>
              <div class="mb-3">
                <label for="categoryDescription" class="form-label">Description</label>
                <textarea class="form-control" id="categoryDescription" rows="3" v-model="currentCategory.description"></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" @click="saveCategory">{{ isEditMode ? 'Save Changes' : 'Create' }}</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import { Modal } from 'bootstrap';

const categories = ref([]);
const loading = ref(true);
const categoryModalRef = ref(null);
let categoryModal = null;

const isEditMode = ref(false);
const currentCategory = ref({ id: null, name: '', description: '' });

const fetchData = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/admin/categories');
    categories.value = response.data;
  } catch (error) {
    console.error("Failed to fetch categories:", error);
  } finally {
    loading.value = false;
  }
};

const openModal = (category = null) => {
  if (category) {
    isEditMode.value = true;
    currentCategory.value = { ...category };
  } else {
    isEditMode.value = false;
    currentCategory.value = { id: null, name: '', description: '' };
  }
  categoryModal.show();
};

const saveCategory = async () => {
  try {
    if (isEditMode.value) {
      await axios.put(`/api/admin/categories/${currentCategory.value.id}`, currentCategory.value);
    } else {
      await axios.post('/api/admin/categories', currentCategory.value);
    }
    categoryModal.hide();
    fetchData(); // Refresh the list
  } catch (error) {
    console.error("Failed to save category:", error);
    // You might want to show an error message to the user here
  }
};

const deleteCategory = async (id) => {
  if (confirm('Are you sure you want to delete this category?')) {
    try {
      await axios.delete(`/api/admin/categories/${id}`);
      fetchData(); // Refresh the list
    } catch (error) {
      console.error("Failed to delete category:", error);
      // You might want to show an error message to the user here
    }
  }
};

onMounted(() => {
  fetchData();
  nextTick(() => {
    if (categoryModalRef.value) {
      categoryModal = new Modal(categoryModalRef.value);
    }
  });
});
</script>