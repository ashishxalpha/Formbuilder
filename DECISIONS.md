# Architectural Decisions

## Database Design: No `form_fields` or `submission_answers` tables
**Decision**: I decided *not* to include traditional relational tables for `form_fields` and `submission_answers`. 
**Reasoning**: A modern Form Builder often requires deeply nested schemas, complex conditional logic, and rapid layout changes. Using an Entity-Attribute-Value (EAV) model for fields and answers creates extreme database overhead (the "N+1" problem for reads, and massive inserts). By storing `schema_data` and `response_data` as JSON within `form_versions` and `submissions` tables, we achieve higher write throughput, perfectly maintain document integrity per version, and avoid complex table joins. Relational databases like MySQL and PostgreSQL now have excellent native JSON support allowing for indexing and querying if needed.

## Schema Compiler & Cache
**Decision**: Implementing a central `SchemaCompiler` that validates and transforms raw JSON into a compiled schema, complete with Laravel validation rules and renderer configs.
**Reasoning**: This prevents the frontend or backend from repeating logic. The raw AI or Builder output goes into the compiler, and everything downstream (validation, rendering, submission) strictly relies on the compiled cache.

## Field Registry
**Decision**: Avoided massive `switch` statements by implementing a `FieldRegistry` and `FieldInterface`.
**Reasoning**: Follows the Open-Closed Principle (SOLID). We can add new field types (e.g., `PaymentField`, `SignatureField`) simply by creating a new class and registering it, without touching core compilation logic.

## Command Pattern for Builder
**Decision**: Implemented `CommandInterface` and `BuilderService` with Undo/Redo stacks.
**Reasoning**: A drag-and-drop builder requires reliable state management. Storing commands allows users to naturally revert mistakes.

## AI HTTP Gateway (FastAPI)
**Decision**: Used a completely separate FastAPI microservice.
**Reasoning**: Separating the AI generation isolates expensive HTTP requests to LLM providers. Laravel's `AIClient` provides the circuit breaking, retries, and error normalization, meaning Laravel never knows *which* AI provider is behind the Python API. This creates a scalable microservice architecture.
