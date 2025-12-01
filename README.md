
# Libya Knowledge Base (RAG-Enabled)

A modern, AI-powered Knowledge Base application built with **Laravel 12**, **Vue.js 3**, and **Bootstrap 5**. This application serves as both a public-facing documentation site and a comprehensive admin panel for managing content and monitoring AI interactions.

It is designed to work in tandem with a Python RAG (Retrieval-Augmented Generation) microservice to provide an intelligent chatbot assistant.

## Key Features

### Public Interface
* **Full Vue 3 SPA:** The entire visitor experience (Home, Categories, Articles, Search) is a fast, interactive Single Page Application built with Vue 3.
* **Interactive Chatbot:** Integrated floating chat component that queries a local LLM (via Python API) to answer user questions based on knowledge base content.
* **Smart Search:** Full-text search capabilities with instant filtering.
* **Responsive Design:** Built with Bootstrap 5.3 and custom CSS.

### Admin Dashboard (SPA)
* **Dedicated Admin SPA:** A separate, protected Vue 3 application for administrators.
* **Secure Authentication:** Powered by Laravel Sanctum (stateful cookie-based auth).
* **Content Management:** Full CRUD for articles, and categories.
* **Rich Text Editor:** Integrated Quill editor with automatic HTML sanitization (HTMLPurifier).
* **RAG Monitoring:**
    * **Sync Status:** Track which articles need to be re-embedded.
    * **Chat History:** View full logs of user queries and AI responses.
    * **Content Gap:** Analyze unanswered questions to improve your KB.
    * **Pipeline Control:** Trigger Python ingestion scripts directly from the UI.

---

## Prerequisites

* **PHP:** 8.4
* **Composer**
* **Node.js** & **NPM** (Tested on 25.2.1 and 11.6.2 respectively)
* **MySQL** (Primary Database, Tested on 8.0)
* **Python Environment** (For the RAG microservice - see separate docs)

---

## Installation

1.  **Clone the Repository**
    
    ```bash
    git clone https://github.com/wesamly/libya-rag-kb.git
    cd libya-rag-kb
    ```

2.  **Install PHP Dependencies**
    
    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies**
    
    ```bash
    npm install
    ```

4.  **Environment Setup**
    Copy the example environment file and configure your variables.
    
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Configure Database (.env)**
    Update your `.env` file with your MySQL credentials.
    
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_kb_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```

6.  **Configure RAG Service (.env)**
    Add the configuration for connecting to your Python API and scripts.
    
    ```ini
    # URL for the Chatbot to call (Ollama/Python API)
    PYTHON_RAG_API_URL=http://127.0.0.1:8000/ask

    # Paths for the "Sync Now" admin button
    PYTHON_PATH=/path/to/your/venv/bin/python
    INGEST_SCRIPT_PATH=/path/to/your/libya_rag_api/ingest.py
    ```

7.  **Run Migrations**
    Create the database tables.
    
    ```bash
    php artisan migrate
    ```
    *Optionally seed a default admin user:*
    
    ```bash
    php artisan db:seed
    ```
    
---

## Running the Application

You need to run both the backend server and the frontend build process.

**Terminal 1: Laravel Server**

```bash
php artisan serve
```

**Terminal 2: Vite (Frontend Assets)**

```bash
npm run dev
```

The application will be available at `http://localhost:8000` (or your configured local domain).

-----

## Architecture Overview

### Dual-SPA Frontend Strategy

This project uses a modern "Dual SPA" approach to separate public and admin concerns while maintaining a smooth user experience:

1.  **Visitor App (`resources/js/app.js`):**

      * **Entry Point:** `resources/views/public.blade.php`
      * **Tech Stack:** Vue 3 + Vue Router.
      * **Function:** Handles the entire public site (Home, Article viewing, Search) as a single-page application for speed and fluidity.
      * **Integration:** The AI Chatbox is a core component of this app, available on every route.

2.  **Admin App (`resources/js/admin.js`):**

      * **Entry Point:** `resources/views/admin.blade.php`
      * **Tech Stack:** Vue 3 + Vue Router + Laravel Sanctum.
      * **Function:** A completely separate, secure SPA for content management and analytics.
      * **Security:** Protected by Laravel's `auth` middleware; the blade file is only served to authenticated users.

### RAG Pipeline Integration

  * **Synchronization Flag:** The `articles` table has a `needs_sync` boolean column.
  * **Auto-Flagging:** When an article is created or updated via the `Article` model, a Mutator automatically cleans the HTML and sets `needs_sync = 1`.
  * **Ingestion:** The Python script queries MySQL for `needs_sync = 1` articles, embeds them, saves to ChromaDB, and then updates MySQL to set `needs_sync = 0`.

-----

## Security Note: HTML Sanitization

This application allows admins to write HTML content. To prevent XSS attacks while preserving formatting for the RAG pipeline, we use **HTMLPurifier** on the backend.

  * **Config:** `config/purifier.php`
  * **Behavior:** Automatically strips dangerous tags (`<script>`, `<iframe>`, `<style>`) and layout tags (`<div>`, `<table>`) but preserves semantic content tags (`<p>`, `<ul>`, `<b>`, etc.) required for the vector embedding process.

-----

## License

Project is released under the [MIT License](https://opensource.org/licenses/MIT).
