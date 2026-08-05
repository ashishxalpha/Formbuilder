from typing import List, Dict, Any
from app.services.ai_provider import AIProvider
import json
import logging

logger = logging.getLogger(__name__)

async def refine_schema_with_ai(raw_fields: List[Dict[str, Any]], provider: AIProvider, model: str) -> List[Dict[str, Any]]:
    """
    Takes a list of deterministically parsed raw fields (with 'label' and 'raw_text')
    and uses AI to infer the exact form field type, options, and validation rules.
    """
    
    prompt = """
    You are an AI assistant helping to build a form schema from an uploaded document.
    I will provide you with a JSON array of extracted raw fields.
    Each object contains an 'id', 'key', 'label', and sometimes 'raw_text' or 'is_section'.
    
    Your task is to analyze the 'label' and 'raw_text' context and determine the best form field type.
    Valid form field types are: text, textarea, email, phone, number, date, dropdown, radio, checkbox, file, rating, section_heading.
    
    Rules:
    1. If 'is_section' is true, type must be 'section_heading'.
    2. If the label asks for an email, use 'email'.
    3. If the label asks for a phone, use 'phone'.
    4. If the label asks for a date (e.g. DOB), use 'date'.
    5. If the label implies a long answer (e.g. "describe", "explain"), use 'textarea'.
    6. If the raw text contains obvious checkboxes or multiple options (e.g. ☐ Yes ☐ No), use 'radio' or 'checkbox' and populate the 'options' array with {"label": "Yes", "value": "yes"}, etc.
    7. Retain the exact 'id' and 'key' provided.
    8. Set 'required' to True if the label has an asterisk (*), else False.
    
    Output MUST be valid JSON containing an array of field objects. Do NOT include markdown blocks.
    
    Input JSON:
    """ + json.dumps(raw_fields, indent=2)

    try:
        response_text = provider.generate(
            model=model,
            system_prompt="You are an expert form schema builder. Respond ONLY with valid JSON.",
            prompt=prompt,
            temperature=0.0
        )
        
        # Clean response if it has markdown ticks
        response_text = response_text.strip()
        if response_text.startswith("```json"):
            response_text = response_text[7:]
        if response_text.startswith("```"):
            response_text = response_text[3:]
        if response_text.endswith("```"):
            response_text = response_text[:-3]
            
        refined_fields = json.loads(response_text)
        return refined_fields
    except Exception as e:
        logger.error(f"Failed to refine schema with AI: {e}")
        # Fallback to basic text
        for f in raw_fields:
            if "type" not in f:
                f["type"] = "section_heading" if f.get("is_section") else "text"
            if "is_section" in f:
                del f["is_section"]
            if "raw_text" in f:
                del f["raw_text"]
        return raw_fields
