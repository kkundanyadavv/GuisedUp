<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query('q', '');

        if (blank($query)) {
            return response()->json([]);
        }

        $response = Http::timeout(10)->post(env('EMBEDDING_SERVICE_URL'), ['text' => $query]);
        $embedding = $response->successful() ? $response->json('embedding') : $this->mockEmbedding($query);

        $posts = Post::query()
            ->select('posts.*')
            ->selectRaw('(1 - (posts.embedding <=> ?)) AS similarity', [$embedding])
            ->orderByDesc('similarity')
            ->limit(10)
            ->get();

        return response()->json($posts);
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
