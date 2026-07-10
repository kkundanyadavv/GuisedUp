<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:2000'],
            'image_url' => ['nullable', 'url'],
        ]);

        $response = Http::timeout(10)->post(env('EMBEDDING_SERVICE_URL'), [
            'text' => $data['text'],
        ]);

        $embedding = $response->successful() ? $response->json('embedding') : $this->mockEmbedding($data['text']);

        $post = Post::create([
            'user_id' => $request->user()->id,
            'text' => $data['text'],
            'image_url' => $data['image_url'] ?? null,
            'embedding' => $embedding,
            'authenticity_score' => $this->scoreAuthenticity($data['text'], $data['image_url'] ?? null),
        ]);

        return response()->json($post->load('user'), 201);
    }

    private function scoreAuthenticity(string $text, ?string $imageUrl): float
    {
        $lengthScore = min(strlen($text) / 200, 1.0);
        $hashtagDensity = substr_count($text, '#') / max(1, str_word_count($text));
        $hashtagPenalty = max(0, $hashtagDensity - 0.1) * 0.25;
        $imageBonus = $imageUrl ? 0.1 : 0.0;
        $score = 0.7 * $lengthScore + 0.2 * (1 - min($hashtagPenalty, 0.2)) + 0.1 * $imageBonus;

        return round(max(0.0, min(1.0, $score)), 4);
    }

    private function mockEmbedding(string $text): array
    {
        $seed = crc32($text);
        mt_srand($seed);
        $vector = [];
        for ($i = 0; $i < 384; $i++) {
            $vector[] = round((mt_rand(-1000, 1000) / 1000), 6);
        }

        return $vector;
    }
}
