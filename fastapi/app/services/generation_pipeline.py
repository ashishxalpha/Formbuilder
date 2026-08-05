from app.services.ai_provider import AIProvider
from app.services.prompt_service import PromptService
import json
import uuid

class GenerationPipeline:
    @staticmethod
    def generate_form(prompt: str, model: str, temperature: float) -> dict:
        system_prompt = PromptService.get_system_prompt("generate")
        raw_json = AIProvider.generate(prompt, system_prompt, model, temperature)
        
        try:
            schema = json.loads(raw_json)
            # Ensure IDs are present
            for field in schema.get('fields', []):
                if 'id' not in field:
                    field['id'] = str(uuid.uuid4())
            return schema
        except json.JSONDecodeError:
            # Fallback repair logic could go here
            return GenerationPipeline.repair_schema(raw_json, "Invalid JSON format")

    @staticmethod
    def edit_form(prompt: str, current_schema: dict) -> dict:
        system_prompt = PromptService.get_system_prompt("edit")
        full_prompt = f"Existing Schema:\n{json.dumps(current_schema)}\n\nUser Request: {prompt}"
        raw_json = AIProvider.generate(full_prompt, system_prompt)
        
        try:
            return json.loads(raw_json)
        except json.JSONDecodeError:
            return current_schema

    @staticmethod
    def repair_schema(malformed_json: str, error_details: str) -> dict:
        system_prompt = PromptService.get_system_prompt("repair")
        full_prompt = f"Malformed JSON:\n{malformed_json}\n\nError: {error_details}"
        raw_json = AIProvider.generate(full_prompt, system_prompt)
        try:
            return json.loads(raw_json)
        except:
            return {"fields": []} # Ultimate fallback
