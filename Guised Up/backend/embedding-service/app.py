from fastapi import FastAPI
from pydantic import BaseModel
import hashlib
import math

app = FastAPI(title='Guised Up Embedding Service')

class EmbedRequest(BaseModel):
    text: str

@app.post('/embed')
def embed(request: EmbedRequest):
    # Mock embedding: production would swap this for sentence-transformers or a hosted model.
    digest = hashlib.sha256(request.text.lower().encode('utf-8')).hexdigest()
    seed = int(digest[:8], 16)
    vector = []
    for i in range(384):
        value = ((seed + i * 97) % 2001 - 1000) / 1000.0
        vector.append(round(value, 6))
    return {'embedding': vector}
