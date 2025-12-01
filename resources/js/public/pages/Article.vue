<template>
  <div class="container mt-5">
    <div class="row g-5">
      <!-- Article Content -->
      <div class="col-lg-8">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb">
            <li v-for="(crumb, index) in article.breadcrumb" :key="index" class="breadcrumb-item">
              <router-link v-if="crumb.slug" :to="crumb.slug">{{ crumb.name }}</router-link>
              <span v-else>{{ crumb.name }}</span>
            </li>
          </ol>
        </nav>

        <!-- Article Header -->
        <h1 class="display-5 fw-bold mb-3">{{ article.title }}</h1>
        <p class="text-muted fs-6">
          Last updated on {{ article.last_updated }} &middot; {{ article.read_time }}
        </p>

        <hr class="my-4">

        <!-- Article Body -->
        <div class="article-body" v-html="article.content"></div>

        <!-- Feedback -->
        <div class="border-top pt-4 mt-5 text-center">
          <h5 class="mb-3">Was this article helpful?</h5>
          <button class="btn btn-outline-success me-2"><i class="bi bi-hand-thumbs-up me-2"></i> Yes</button>
          <button class="btn btn-outline-danger"><i class="bi bi-hand-thumbs-down me-2"></i> No</button>
        </div>

        <!-- Related Articles -->
        <section class="mt-5 pt-5 border-top">
          <h3 class="fw-bold mb-4">Related Articles</h3>
          <div class="row g-4">
            <div v-for="related in related_articles" :key="related.slug" class="col-md-4">
              <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                  <h5 class="card-title fw-bold mb-2">{{ related.title }}</h5>
                  <p class="card-text text-muted small">{{ related.description }}</p>
                  <router-link :to="{ name: 'article', params: { slug: related.slug } }" class="fw-medium text-decoration-none">
                    Read more <i class="bi bi-arrow-right-short"></i>
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </section>

      </div>

      <!-- Sticky Table of Contents -->
      <div class="col-lg-4 d-none d-lg-block">
        <div class="sticky-toc" v-if="article.toc.length > 0">
          <h6 class="text-uppercase text-muted small fw-bold mb-3">On this page</h6>
          <nav class="nav nav-pills flex-column toc-nav">
            <a 
              v-for="item in article.toc" 
              :key="item.id" 
              class="nav-link" 
              :href="'#' + item.id"
            >
              {{ item.title }}
            </a>
          </nav>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const article = ref({
  breadcrumb: [],
  toc: []
});
const related_articles = ref([]);
const isLoading = ref(false);

const props = defineProps({
  slug: String
});

const fetchArticle = async (slug) => {
  if (isLoading.value) return;
  isLoading.value = true;
  try {
    const response = await axios.get(`/api/public/article/${slug}`);
    article.value = response.data.article;
    related_articles.value = response.data.related_articles;
  } catch (error) {
    console.error('Failed to fetch article:', error);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchArticle(props.slug);
});

// Watch for changes in the route (e.g., clicking a related article link)
watch(() => props.slug, (newSlug) => {
  fetchArticle(newSlug);
});
</script>

<style scoped>
/* Scoped styles for the article body to style the v-html content */
.article-body :deep(h4) {
  font-weight: 600;
  margin-top: 2rem;
  margin-bottom: 1rem;
}
.article-body :deep(ul) {
  margin-bottom: 1rem;
}

.toc-nav .nav-link {
  color: #495057;
  padding: 0.3rem 0;
  font-size: 0.9rem;
}
.toc-nav .nav-link:hover,
.toc-nav .nav-link.active {
  color: #0d6efd;
  font-weight: 500;
}
</style>