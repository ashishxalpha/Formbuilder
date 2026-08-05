from fastapi import APIRouter, UploadFile, File
import docx
import openpyxl
import io
import uuid

router = APIRouter()

@router.post("/docx")
async def parse_docx(file: UploadFile = File(...)):
    content = await file.read()
    doc = docx.Document(io.BytesIO(content))
    
    text_blocks = []
    for para in doc.paragraphs:
        if para.text.strip():
            text_blocks.append(para.text.strip())
            
    # Simple heuristic fallback (usually AI handles this)
    fields = []
    for idx, text in enumerate(text_blocks):
        fields.append({
            "id": str(uuid.uuid4()),
            "key": f"field_{idx}",
            "type": "text",
            "label": text[:100], # First 100 chars as label
            "required": False
        })

    return {
        "schema": {
            "version": "1.0.0",
            "metadata": {"title": file.filename},
            "fields": fields,
            "layout": {"sections": [{"id": "s1", "fields": [f["id"] for f in fields]}]}
        },
        "warnings": ["AI extraction skipped, using raw text mapping"]
    }

@router.post("/xlsx")
async def parse_xlsx(file: UploadFile = File(...)):
    content = await file.read()
    wb = openpyxl.load_workbook(io.BytesIO(content), data_only=True)
    sheet = wb.active
    
    fields = []
    headers = [cell.value for cell in sheet[1]] if sheet.max_row > 0 else []
    
    for idx, header in enumerate(headers):
        if not header: continue
        fields.append({
            "id": str(uuid.uuid4()),
            "key": f"col_{idx}",
            "type": "text",
            "label": str(header),
            "required": False
        })

    return {
        "schema": {
            "version": "1.0.0",
            "metadata": {"title": file.filename},
            "fields": fields,
            "layout": {"sections": [{"id": "s1", "fields": [f["id"] for f in fields]}]}
        },
        "warnings": ["Using header row mapping"]
    }
