from fastapi import APIRouter
from app.schemas.forms import (
    GenerateFormRequest,
    EditFormRequest,
    RepairSchemaRequest,
    InferFieldTypesRequest,
    InferValidationsRequest,
    TranslateFormRequest
)
from app.services.generation_pipeline import GenerationPipeline

router = APIRouter()

@router.post("/generate")
async def generate_form(request: GenerateFormRequest):
    schema = GenerationPipeline.generate_form(request.prompt, request.model, request.temperature)
    return {
        "schema": schema,
        "input_tokens": 0, # Mock token counts if not using streaming/response metadata
        "output_tokens": 0,
        "prompt_version": "v1.0"
    }

@router.post("/edit")
async def edit_form(request: EditFormRequest):
    schema = GenerationPipeline.edit_form(request.prompt, request.schema_data)
    return {
        "schema": schema,
        "input_tokens": 0,
        "output_tokens": 0
    }

@router.post("/repair")
async def repair_schema(request: RepairSchemaRequest):
    schema = GenerationPipeline.repair_schema(request.malformed_json, request.error_details)
    return {
        "schema": schema,
        "success": True,
        "error_message": None
    }

@router.post("/translate")
async def translate_form(request: TranslateFormRequest):
    # Simplified mock for translate for now
    return {"schema": request.schema_data}

@router.post("/infer-validations")
async def infer_validations(request: InferValidationsRequest):
    return {"schema": request.schema_data}

@router.post("/infer-field-types")
async def infer_field_types(request: InferFieldTypesRequest):
    return {"fields": request.fields}
