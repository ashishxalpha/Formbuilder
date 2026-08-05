# AI Form Builder Backend Foundation

This project establishes a production-quality SaaS backend foundation for an AI-powered form builder, architected with a strict separation between a Laravel web application and a FastAPI microservice.

## Features Implemented
- **Robust Database & Eloquent Models**: Optimized schema utilizing JSON columns (`schema_data`, `response_data`) to prevent EAV overhead.
- **Strict Schema Compilation**: `SchemaCompiler` ensuring the builder, AI, and submissions operate on a single source of truth.
- **Enterprise Pattern Foundations**: Implementations of the Command Pattern (for Builder undo/redo) and a polymorphic `FieldRegistry`.
- **AI Gateway & Observability**: Laravel's `AIClient` handles circuit breaking, retries, and errors before delegating to the separate Python FastAPI microservice.
- **Event-Driven & Activity Logging**: Full activity logs tracking creations, updates, and AI generations.
- **Queueing & Caching**: Laravel Horizon and Redis cache invalidation strategies pre-configured for scale.

## Running Tests
Tests are written with Pest. Run them with:
`./vendor/bin/pest`
