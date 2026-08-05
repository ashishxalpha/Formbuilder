# AI Form Builder System

This project is a complete, production-ready SaaS backend foundation for an AI-powered form builder, architected with a strict separation between a Laravel backend and a FastAPI Python microservice for AI processing.

## Live Demo & Credentials
**Live URL**: [https://demo.ashishxalpha.me/](https://demo.ashishxalpha.me/) 
*(Note: This demo is restricted to 10 AI operations to prevent API abuse. The system runs seamlessly on a `$10/mo` DigitalOcean droplet using Docker Compose).*

**Credentials**:
- Email: `demo@example.com`
- Password: `password`

## Setup Steps
This project relies entirely on Docker Compose for a zero-friction setup.

1. **Clone the repository**
2. **Environment Variables**:
   - `laravel/.env` (Duplicate from `.env.example`, no external keys required).
   - `fastapi/.env` (Duplicate from `.env.example`, requires `OPENAI_API_KEY`).
3. **Build and Run**:
   ```bash
   docker compose up -d --build
   ```
4. **Run Migrations & Seeders**:
   ```bash
   docker compose exec laravel php artisan migrate:fresh --seed
   ```
5. **Access the application**: 
   - Dashboard: `http://localhost:8080/dashboard`

## Architecture Overview
- **Laravel (Web / API / Queue / Auth)**: The core system. Handles user authentication, database persistence, queueing (via Redis), schema compilation, real-time builder canvas rendering (Livewire), and submission validation.
- **FastAPI (AI / Parsing)**: A Python microservice dedicated entirely to blocking ML tasks. It isolates AI interactions (OpenAI), handles retries and fallback logic, and deterministically parses imported `.docx` and `.xlsx` files using `python-docx` and `openpyxl`.
- **Communication**: Laravel communicates with FastAPI via a resilient HTTP `AIClient` (with timeouts and circuit-breaking logic), strictly via background queue workers to prevent blocking web requests.

## Schema / ERD Summary
The database strictly avoids the EAV (Entity-Attribute-Value) anti-pattern for scalability.
- `users`: Standard auth table.
- `forms`: Core entity (status, title, active_version_id). Indexed on `user_id`, `status` for rapid dashboard loading.
- `form_versions`: Immutable schema snapshots (`schema_data` as JSON). Indexed on `form_id`.
- `submissions`: Contains `response_data` as JSON. Indexed on `form_id`, `form_version_id`.
- `ai_jobs` / `import_jobs`: Tracking tables for background processes. Indexed on `form_id`.

At massive scale, MySQL 8 generated virtual columns with BTREE indexes would be added to specific high-traffic JSON fields inside `submissions`.

## API Endpoints (FastAPI Internal)
- `POST /generate`: Accepts a natural language prompt and returns a valid form JSON schema.
- `POST /imports/docx`: Accepts a Word file upload, deterministically parses elements, and uses hybrid AI inference for type detection.
- `POST /imports/xlsx`: Accepts an Excel file upload and extracts row headers.

## AI Prompt Strategy
Documented extensively in [PromptStrategy.md](./PromptStrategy.md). It details the system prompt, JSON contract forcing, and fallback mechanisms for hallucinations.

## Missed Requirements (Time Constraints) & How I'd Implement Them
Due to the strict time constraint of this assessment, the following items were scoped out. Here is exactly how I would implement them with more time:

1. **Advanced Validation Rules (Regex, URL, File Size/Type)**:
   - *Current*: Supports Required and Min/Max properties (dynamically toggling for Number vs Text fields).
   - *Implementation*: Add these properties to the `FieldRegistry` definitions. In Laravel, I would map these directly to native rules (e.g., `regex:/pattern/`, `url`, `mimes:png,pdf`, `max:2048`) inside the `SchemaCompiler`, instantly providing server-side validation during form submission.

2. **Visual Multi-Step Wizard**:
   - *Current*: Forms can be logically grouped by "Section Headings", but they render on a single continuous page.
   - *Implementation*: Modify the `PublicFormRenderer` Livewire component. I would track a `public $currentStepIndex = 0` state variable, slicing the compiled layout schema arrays. A "Next" button would validate only the fields in the current slice before advancing.

3. **Complex Excel Parsing Layouts**:
   - *Current*: Parses standard header-row sheets deterministically.
   - *Implementation*: I would write heuristic scanners in `openpyxl` to detect grid blocks (e.g. searching for dense clusters of cells) if a header isn't strictly on Row 1, and pipe those grid blocks to an LLM for structured layout mapping.
