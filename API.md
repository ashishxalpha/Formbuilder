# API Documentation

## Laravel Internal API (Livewire / Controllers)
- `GET /forms/{form}/builder` - Access Drag & Drop Builder
- `GET /forms/{form}/dashboard` - Access Form Activity and Jobs
- `GET /templates` - View Template Library
- `GET /f/{formVersion}` - Public Renderer Endpoint (Submission)

## FastAPI Microservice
Base URL: `http://fastapi:8000`

### Forms API
- `POST /generate`
  - Body: `{ "prompt": "string", "model": "gpt-4o", "temperature": 0.7 }`
  - Returns: `{ "schema": {...}, "input_tokens": int, "output_tokens": int }`
- `POST /edit`
  - Body: `{ "schema_data": {...}, "prompt": "string" }`
  - Returns: Edited schema
- `POST /repair`
  - Body: `{ "malformed_json": "string", "error_details": "string" }`
  - Returns: Valid JSON schema

### Imports API
- `POST /docx` (multipart/form-data)
- `POST /xlsx` (multipart/form-data)
  - Returns parsed JSON schema representation of the document.
