<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 20;

        $query = Post::query()
            ->select('posts.*')
            ->selectRaw("(
                0.45 * posts.authenticity_score +
                0.25 * CASE WHEN follows.follower_id IS NOT NULL THEN 1 ELSE 0 END +
                0.20 * COALESCE(similarity_score, 0) +
                0.10 * GREATEST(0, 1 - (EXTRACT(EPOCH FROM (NOW() - posts.created_at)) / 86400 / 10))
            ) AS rank_score")
            ->leftJoinSub(function ($query) use ($userId) {
                $query->from('follows')
                    ->selectRaw('followed_id AS followed_id, 1 AS relation_depth')
                    ->where('follower_id', $userId);
            }, 'follows', 'follows.followed_id', '=', 'posts.user_id')
            ->leftJoinSub(function ($query) use ($userId) {
                $query->from('interactions')
                    ->selectRaw('post_id, COUNT(*) AS interaction_count')
                    ->where('user_id', $userId)
                    ->groupBy('post_id');
            }, 'user_interactions', 'user_interactions.post_id', '=', 'posts.id')
            ->leftJoinSub(function ($query) use ($userId) {
                $query->from('posts as p2')
                    ->selectRaw('id, 1 - (embedding <=> (SELECT embedding FROM posts WHERE id = ?)) AS similarity_score', [$userId])
                    ->where('id', '!=', $userId);
            }, 'semantic', 'semantic.id', '=', 'posts.id');

        $posts = $query->orderByDesc('rank_score')->orderByDesc('posts.created_at')->paginate($perPage, ['*'], 'page', $page);

        return response()->json($posts);
    }
}
