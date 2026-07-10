# Guised Up — Real Connections Feed

This project implements a take-home assessment for a social feed experience that combines Laravel for the API, a Python FastAPI embedding microservice, PostgreSQL with pgvector support, and a React Native client. The feed is personalized using a weighted ranking formula that blends authenticity, relationship depth, semantic similarity, and recency.

## Laravel setup

1. Install PHP dependencies:
   `composer install`
2. Copy the example environment file:
   `cp .env.example .env`
3. Create the PostgreSQL database and update the connection settings in `.env`.
4. Run the migrations and seed the database:
   `php artisan migrate --seed`

## Python embedding service

1. Create and activate a virtual environment:
   `python -m venv .venv`
   `source .venv/bin/activate` (or `.venv\\Scripts\\activate` on Windows)
2. Install dependencies:
   `pip install -r requirements.txt`
3. Start the service:
   `uvicorn app:app --host 0.0.0.0 --port 8001`

## React Native setup

1. Install dependencies:
   `npm install`
2. Start the app in a simulator or on a device:
   `npx react-native run-android` or `npx react-native run-ios`

## Environment example

See `.env.example` for the Laravel configuration values.

## Notes on what was mocked

Embeddings are generated deterministically from the input text rather than calling an external AI provider. This matches the assessment requirement of avoiding external API credits while still producing a stable vector representation for search and ranking.

## What is incomplete / next steps

The current implementation focuses on the core API, seed data, feed ranking, and a polished mobile feed screen. The next steps would be to tighten the ranking SQL query to use the exact TSD weighting, add more realistic follow graph logic, and expand test coverage for edge cases and interaction logging.
