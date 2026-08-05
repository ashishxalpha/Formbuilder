from pydantic import BaseModel, Field
from typing import Optional, Dict, Any, List

class GenerateFormRequest(BaseModel):
    prompt: str
    model: Optional[str] = "gpt-4o-mini"
    temperature: Optional[float] = 0.7

class EditFormRequest(BaseModel):
    prompt: str
    schema_data: Dict[str, Any]

class RepairSchemaRequest(BaseModel):
    malformed_json: str
    error_details: str

class InferFieldTypesRequest(BaseModel):
    fields: List[Dict[str, Any]]

class InferValidationsRequest(BaseModel):
    schema_data: Dict[str, Any]

class TranslateFormRequest(BaseModel):
    schema_data: Dict[str, Any]
    target_language: str
