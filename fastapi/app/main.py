from fastapi import FastAPI
from app.routers import forms, imports
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI(
    title="AI Form Builder Service",
    description="Microservice for AI form generation, editing, and parsing.",
    version="1.0.0"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/health")
def health_check():
    return {"status": "ok", "service": "fastapi-ai"}

# To be implemented
# app.include_router(forms.router, prefix="/forms", tags=["forms"])
# app.include_router(imports.router, prefix="/imports", tags=["imports"])
