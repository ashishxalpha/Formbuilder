class PromptService:
    @staticmethod
    def get_system_prompt(task: str) -> str:
        base_contract = """
        You are an expert Form Builder Architect.
        OUTPUT CONTRACT:
        You must output ONLY valid JSON.
        The JSON must match this schema:
        {
          "version": "1.0.0",
          "metadata": {"title": "String", "description": "String"},
          "fields": [
             {
                "id": "uuid",
                "key": "unique_snake_case_string",
                "type": "text|textarea|email|phone|number|date|dropdown|radio|checkbox|file|rating|section_heading",
                "label": "String",
                "required": Boolean,
                "options": [{"label": "String", "value": "String"}] (only for dropdown/radio/checkbox),
                "validation": {"min": int, "max": int, "mimes": ["png", "jpg"]} (optional)
             }
          ],
          "layout": {
             "sections": [
                 {"id": "section_uuid", "fields": ["field_uuid"]}
             ]
          }
        }
        """
        
        tasks = {
            "generate": base_contract + "\nGenerate a complete form schema based on the user's prompt.",
            "edit": base_contract + "\nThe user will provide an existing schema and a prompt. Modify the schema accordingly.",
            "repair": base_contract + "\nThe user will provide malformed JSON and error details. Return a repaired, fully valid JSON schema.",
            "translate": base_contract + "\nTranslate all human-readable text (labels, descriptions, option labels, metadata) to the target language. Do not change keys or IDs.",
        }
        return tasks.get(task, base_contract)
