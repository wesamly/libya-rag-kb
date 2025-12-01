<template>
  <div class="chatbox-container">
    <!-- Chat Window (conditionally rendered) -->
    <div v-if="isOpen" class="chat-window shadow-lg border rounded-3">
      <!-- Header -->
      <div class="chat-header p-3 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-robot fs-5 text-primary"></i>
          <h6 class="mb-0 fw-bold">Knowledge Assistant</h6>
        </div>
        <button @click="isOpen = false" type="button" class="btn-close" aria-label="Close"></button>
      </div>

      <!-- Message History -->
      <div class="chat-body p-3" ref="chatBody">
        <!-- Welcome Message -->
        <div class="message assistant mb-3">
          <div class="message-bubble">
            Hi! I'm here to help you find answers from our knowledge base. What can I help you with today?
          </div>
        </div>

        <!-- Suggested Prompts -->
        <div class="suggested-prompts d-flex flex-column align-items-end mb-3">
          <button @click="askQuestion('How does X work?')" class="btn btn-outline-primary btn-sm mb-2">How does X work?</button>
          <button @click="askQuestion('Compare A and B')" class="btn btn-outline-primary btn-sm">Compare A and B</button>
        </div>

        <!-- Dynamic Messages -->
        <div v-for="(msg, index) in messages" :key="index" :class="['message', msg.role, 'mb-3']">
          <div class="message-bubble">
            <span v-html="msg.content"></span>
            
            <!-- Sources (for assistant replies) -->
            <div v-if="msg.role === 'assistant' && msg.sources" class="mt-3">
              <hr class="my-2">
              <small class="fw-bold d-block mb-1">Sources:</small>
              <ul class="list-unstyled mb-0">
                <li v-for="source in msg.sources" :key="source.id">
                  <small>
                    <router-link :to="{ name: 'article', params: { slug: source.slug } }" @click="isOpen = false">
                      <i class="bi bi-file-text me-1"></i> {{ source.title }}
                    </router-link>
                  </small>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Loading Indicator -->
        <div v-if="isLoading" class="message assistant mb-3">
          <div class="message-bubble">
            <div class="spinner-border spinner-border-sm" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Input Form -->
      <div class="chat-footer p-3 border-top">
        <form @submit.prevent="sendMessage" class="position-relative">
          <input 
            v-model="newMessage"
            type="text" 
            class="form-control" 
            placeholder="Ask a question..."
            :disabled="isLoading"
          >
          <button type="submit" class="btn btn-primary position-absolute" :disabled="isLoading">
            <i class="bi bi-send-fill"></i>
          </button>
        </form>
      </div>
    </div>

    <!-- Launcher Button -->
    <button 
      @click="isOpen = !isOpen" 
      class="chat-launcher btn btn-primary shadow-lg" 
      type="button" 
      aria-label="Open chat"
    >
      <i class="bi bi-chat-right-text-fill fs-4"></i>
    </button>
  </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';

const isOpen = ref(false);
const isLoading = ref(false);
const newMessage = ref('');
const messages = ref([]);
const chatBody = ref(null);

const scrollToBottom = () => {
  nextTick(() => {
    if (chatBody.value) {
      chatBody.value.scrollTop = chatBody.value.scrollHeight;
    }
  });
};

const askQuestion = (question) => {
  newMessage.value = question;
  sendMessage();
};

const sendMessage = async () => {
  if (newMessage.value.trim() === '' || isLoading.value) return;

  const userMessage = newMessage.value;
  messages.value.push({ role: 'user', content: userMessage });
  isLoading.value = true;
  newMessage.value = '';
  scrollToBottom();

  try {
    const response = await axios.post('/chat', { question: userMessage });
    const data = response.data;

    messages.value.push({ 
      role: 'assistant', 
      content: data.answer,
      sources: data.sources // Attach the sources
    });

  } catch (error) {
    console.error('Error fetching chat response:', error);
    messages.value.push({ 
      role: 'assistant', 
      content: 'Sorry, I seem to be having trouble. Please try again.' 
    });
  } finally {
    isLoading.value = false;
    scrollToBottom();
  }
};
</script>

<style scoped>
.chatbox-container {
  position: fixed;
  bottom: 1.5rem;
  right: 1.5rem;
  z-index: 1050;
}

.chat-launcher {
  width: 4rem;
  height: 4rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chat-window {
  width: 400px;
  max-width: calc(100vw - 3rem);
  height: 70vh;
  max-height: 600px;
  display: flex;
  flex-direction: column;
  background: #fff;
}

.chat-body {
  flex-grow: 1;
  overflow-y: auto;
}

.message .message-bubble {
  padding: 0.75rem 1rem;
  border-radius: 1rem;
  max-width: 85%;
  display: inline-block;
  word-wrap: break-word;
}

.message.assistant .message-bubble {
  background-color: #f1f3f5;
  color: #212529;
  border-bottom-left-radius: 0.25rem;
}

.message.user {
  display: flex;
  justify-content: flex-end;
}

.message.user .message-bubble {
  background-color: #0d6efd;
  color: white;
  border-bottom-right-radius: 0.25rem;
}

.chat-footer .form-control {
  padding-right: 3.5rem;
  border-radius: 0.75rem;
}

.chat-footer .btn {
  top: 50%;
  right: 0.5rem;
  transform: translateY(-50%);
  border-radius: 0.5rem;
}

.suggested-prompts button {
  max-width: 85%;
  white-space: normal;
  text-align: right;
  font-weight: 500;
  border-radius: 0.75rem;
  border-bottom-right-radius: 0.25rem;
}
</style>