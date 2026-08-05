from fastapi import APIRouter, UploadFile, File

router = APIRouter()

@router.post("/docx")
async def parse_docx(file: UploadFile = File(...)):
    # Mock response
    return {
        "schema": {
            "version": "1.0.0",
            "metadata": {"title": file.filename},
            "fields": []
        },
        "warnings": []
    }

@router.post("/xlsx")
async def parse_xlsx(file: UploadFile = File(...)):
    # Mock response
    return {
        "schema": {
            "version": "1.0.0",
            "metadata": {"title": file.filename},
            "fields": []
        },
        "warnings": []
    }
