# Prompt Engineering Strategy

## Abstraction
We isolate prompt engineering entirely within the Python `FastAPI` service (`app/services/prompt_service.py`). Laravel makes a structured POST request without knowing the LLM context.

## Output Contract
Our system prompt strictly defines the `JSON Schema` structure required. LLMs are instructed to use JSON Mode.

## Repair Pipeline
If `GenerationPipeline.generate_form` catches a `json.JSONDecodeError`, it falls back to `repair_schema()`, supplying the LLM with the exact `malformed_json` and the Python error details. This effectively provides self-correction without Laravel knowing.

## Hallucination Handling
By forcing strict Pydantic parsing on the FastAPI side, we ensure hallucinations are stripped or rejected. Field types must match our exact enum (`text, textarea, date, ...`).
