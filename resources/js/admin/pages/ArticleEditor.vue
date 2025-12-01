<template>
  <form v-if="loading" class="text-center p-5">
    <div class="spinner-border" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </form>
  <form v-else @submit.prevent="saveArticle('published')">
    <div class="row g-4">
      <!-- Main Content Area -->
      <div class="col-lg-9">
        <div class="card h-100">
          <div class="card-body d-flex flex-column">
            <!-- Article Title -->
            <div class="mb-3">
              <label for="articleTitle" class="form-label visually-hidden">Article Title</label>
              <input 
                type="text" 
                class="form-control form-control-lg" 
                id="articleTitle" 
                placeholder="Article Title..."
                v-model="article.title"
                required
                style="font-size: 1.5rem; font-weight: 600; background-color: transparent; border: 0; padding-left: 0; box-shadow: none;"
              >
            </div>
            
            <!-- Content Editor -->
            <div class="flex-grow-1" style="min-height: 400px; display: flex; flex-direction: column;">
              <QuillEditor
                v-model:content="article.content"
                :toolbar="toolbarOptions"
                theme="snow"
                content-type="html"
                placeholder="Start writing your article here..."
                style="display: flex; flex-direction: column; height: 100%;"
              />
            </div>

            <div class="text-end text-muted small mt-auto pt-2">
              <span v-if="lastSaveTime">Last saved: {{ lastSaveTime }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Settings Sidebar -->
      <div class="col-lg-3">
        <!-- Publish Card -->
        <div class="card mb-4">
          <div class="card-header">
            <h6 class="mb-0 fw-semibold">Publish</h6>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="fw-medium">Status:</span>
              <span :class="['badge', statusClass(article.status)]">{{ article.status }}</span>
            </div>
            <div class="d-grid gap-2">
              <button 
                type="button" 
                class="btn btn-secondary"
                @click.prevent="saveArticle('draft')"
                :disabled="isSaving"
              >
                <span v-if="isSaving && lastSavedStatus === 'draft'" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Save Draft
              </button>
              <button 
                type="submit" 
                class="btn btn-primary"
                :disabled="isSaving"
              >
                <span v-if="isSaving && lastSavedStatus === 'published'" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Publish
              </button>
            </div>
          </div>
        </div>

        <!-- Category Card -->
        <div class="card mb-4">
          <div class="card-header">
            <h6 class="mb-0 fw-semibold">Category</h6>
          </div>
          <div class="card-body">
            <select class="form-select" v-model="article.category_id" required>
              <option value="0" disabled>Select a category</option>
              <option v-for="cat in categories" :key="cat.value" :value="cat.value">
                {{ cat.label }}
              </option>
            </select>
          </div>
        </div>
        
        <!-- Tags Card -->
        <div class="card mb-4">
          <div class="card-header">
            <h6 class="mb-0 fw-semibold">Tags</h6>
          </div>
          <div class="card-body">
            <!-- This should be replaced with a proper tag-input component, but is functional -->
            <input 
              type="text" 
              class="form-control" 
              placeholder="Add tags..."
              @keydown.enter.prevent="addTag"
            >
            <div class="d-flex flex-wrap gap-2 mt-3">
              <span v-for="(tag, index) in article.tags" :key="index" class="badge bg-primary d-flex align-items-center" style="padding: 0.4em 0.75em; font-size: 0.85rem;">
                {{ tag.label }}
                <button @click.prevent="removeTag(index)" class="btn-close btn-close-white ms-1" style="font-size: 0.6rem;"></button>
              </span>
            </div>
          </div>
        </div>

      </div>
    </div>
  </form>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

// Import Vue Quill
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';

const props = defineProps({
  id: String // From the router
});

const route = useRoute();
const router = useRouter();

// Main data object for the form
const article = ref({
  title: '',
  category_id: 0,
  status: 'draft',
  tags: [] 
});

const categories = ref([]);
const allTags = ref([]); 

const loading = ref(true);
const isSaving = ref(false);
const lastSavedStatus = ref('');
const lastSaveTime = ref('');

// Quill Editor Toolbar Config
const toolbarOptions = [
  ['bold', 'italic', 'underline', 'strike'],        // b, i, u, s
  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],         // h1-h6
  [{ 'list': 'ordered'}, { 'list': 'bullet' }],    // ol, ul
  ['blockquote', 'code-block'],                    // blockquote, pre
  ['link'],                                        // a
  ['clean']                                        // remove formatting
];

// Computed property for status badge class
const statusClass = (status) => {
  if (status === 'published') return 'bg-success';
  if (status === 'draft') return 'bg-secondary';
  return 'bg-secondary';
};

const fetchData = async () => {
  loading.value = true;
  try {
    // 1. Fetch categories and tags for the dropdowns
    const editorData = await axios.get('/api/admin/articles/editor-data');
    categories.value = editorData.data.categories;
    allTags.value = editorData.data.tags;

    // 2. If we are editing, fetch the article data
    if (props.id) {
      const response = await axios.get(`/api/admin/articles/${props.id}`);
      article.value = response.data;
      
    } else {
      // We are creating a new article
      article.value.title = route.query.title || ''; // Pre-fill from content gap
      // Reset all other fields
      article.value.content = '';
      article.value.category_id = 0;
      article.value.status = 'draft';
      article.value.tags = [];
    }

  } catch (error) {
    console.error("Failed to load editor data:", error);
    // TODO: Show error to user
  } finally {
    loading.value = false;
  }
};

const saveArticle = async (statusOverride = null) => {
  isSaving.value = true;

  if (statusOverride) {
    article.value.status = statusOverride;
  }
  lastSavedStatus.value = article.value.status;
  
  // Sync the editor's content back to the main object *before* saving.
  const payload = {
    ...article.value,
    tags: article.value.tags?.map(tag => tag.value) 
  };
  
  try {
    let response;
    if (props.id) {
      // Update existing article
      response = await axios.put(`/api/admin/articles/${props.id}`, payload);
    } else {
      // Create new article
      response = await axios.post('/api/admin/articles', payload);
    }
    
    // After creating, redirect to the edit page
    if (!props.id) {
      router.push({ name: 'admin.articles.edit', params: { id: response.data.data.id } });
    } else {
      // Refresh data to show "All changes saved"
      const updatedArticle = response.data;
      article.value.status = updatedArticle.status;
      article.value.tags = updatedArticle.tags?.map(tag => ({ value: tag.id, label: tag.name }));

      lastSaveTime.value = new Date().toLocaleTimeString();
    }

  } catch (error) {
    console.error("Failed to save article:", error);
    if (error.response && error.response.status === 422) {
      alert("Validation Error: " + JSON.stringify(error.response.data.errors));
    } else {
      alert("An error occurred while saving.");
    }
  } finally {
    isSaving.value = false;
  }
};

const addTag = (event) => {
  const newTagLabel = event.target.value.trim();
  if (newTagLabel.length === 0) return;
  
  let existingTag = allTags.value.find(t => t.label.toLowerCase() === newTagLabel.toLowerCase());
  
  if (existingTag) {
    if (!article.value.tags.find(t => t.value === existingTag.value)) {
      article.value.tags.push(existingTag);
    }
  } else {
    alert("This demo only supports adding existing tags.");
  }
  
  event.target.value = ''; // Clear input
};

const removeTag = (index) => {
  article.value.tags.splice(index, 1);
};

onMounted(fetchData);

</script>

<style scoped>
/* Force Quill to match the dark theme */
:deep(.ql-toolbar) {
  border-color: #30363d !important;
  border-top-left-radius: 0.375rem;
  border-top-right-radius: 0.375rem;
}
:deep(.ql-container) {
  border-color: #30363d !important;
  border-bottom-left-radius: 0.375rem;
  border-bottom-right-radius: 0.375rem;
  flex-grow: 1; /* This is key for it to fill the space */
  display: flex;
  flex-direction: column;
}
:deep(.ql-editor) {
  flex-grow: 1;
  color: #c9d1d9;
  font-size: 1rem;
}
:deep(.ql-editor.ql-blank::before) {
  color: #8b949e;
  font-style: normal;
}
:deep(.ql-stroke) {
  stroke: #8b949e;
}
:deep(.ql-picker-label) {
  color: #8b949e;
}
</style>