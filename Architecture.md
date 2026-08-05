# Enterprise Architecture

## Separation of Concerns
1. **Laravel**: Handles Authentication (Breeze), Database (MySQL), Schema Compilation, Caching (Redis), Queueing (Horizon), and API routing for the frontend Builder.
2. **FastAPI**: A dedicated Python microservice handling AI logic, document processing, and prompt engineering.

## JSON Schema as the Source of Truth
We avoid traditional EAV (Entity-Attribute-Value) models for storing form fields. Instead, the entire structure of a form is a validated JSON schema. 
The **Schema Compiler** takes raw input, validates it, injects default parameters, and outputs a compiled schema along with Laravel validation rules and frontend renderer config.

## AI API Gateway Pattern
Laravel does not implement LLM SDKs directly. The `AIClient` serves as an API Gateway to FastAPI. It is responsible for:
- Request timeouts
- Retries
- Error Normalization
- Logging / Metrics

This ensures Laravel code remains completely agnostic to whether OpenAI, Anthropic, or an open-source model is used.
