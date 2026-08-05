from typing import Dict, Any, Optional
import json
import os
from openai import OpenAI

client = OpenAI(api_key=os.getenv("OPENAI_API_KEY", "sk-mock"))

class AIProvider:
    @staticmethod
    def generate(prompt: str, system_prompt: str, model: str = "gpt-4o-mini", temperature: float = 0.7) -> str:
        # Mocking for local tests without key, but code is production-ready
        if os.getenv("OPENAI_API_KEY", "sk-mock") == "sk-mock":
            return json.dumps({
                "version": "1.0.0",
                "metadata": {"title": "Generated Form", "description": "Mocked generation"},
                "fields": [
                    {"id": "uuid-1", "key": "first_name", "type": "text", "label": "First Name", "required": True}
                ],
                "layout": {"sections": [{"id": "s1", "fields": ["uuid-1"]}]}
            })

        response = client.chat.completions.create(
            model=model,
            messages=[
                {"role": "system", "content": system_prompt},
                {"role": "user", "content": prompt}
            ],
            temperature=temperature,
            response_format={"type": "json_object"}
        )
        return response.choices[0].message.content
