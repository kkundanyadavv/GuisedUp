<?php

namespace Database\Seeders;

use App\Models\Follow;
use App\Models\Interaction;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            User::create(['name' => 'Mina', 'email' => 'mina@example.com', 'password' => Hash::make('secret123')]),
            User::create(['name' => 'Theo', 'email' => 'theo@example.com', 'password' => Hash::make('secret123')]),
        ];

        foreach ($users as $user) {
            $this->makePosts($user);
        }

        Follow::create(['follower_id' => $users[0]->id, 'followed_id' => $users[1]->id]);
        Follow::create(['follower_id' => $users[1]->id, 'followed_id' => $users[0]->id]);

        $posts = Post::all();
        foreach ($posts as $post) {
            if ($post->id % 2 === 0) {
                Interaction::create(['user_id' => $users[0]->id, 'post_id' => $post->id, 'type' => 'view']);
            }
        }
    }

    private function makePosts(User $user): void
    {
        $samples = [
            'Coffee mornings and quiet journaling set the tone for a grounded day.',
            'A new mural downtown feels like a burst of optimism and color.',
            'Weekend plans are simple: walk the river, try a new brunch spot, and talk for hours.',
            'A shared playlist can turn any commute into a thoughtful little ritual.',
            'I am learning to appreciate slow progress over perfect output.',
            'The best conversations usually start with a small question and a little patience.',
            'Someone shared a story that changed how I think about consistency and care.',
            'The city feels different after rain, calmer and more open.',
            'I keep collecting tiny habits that make ordinary days feel more meaningful.',
            'A simple dinner with good music and a long talk is my ideal evening.',
            'I love seeing how people turn small ideas into something generous and useful.',
            'This week reminded me that honest recommendations matter more than marketing.',
            'There is beauty in the routine of checking in with people you care about.',
            'A favorite book can become a perfect conversation starter at the right moment.',
            'I am trying to keep space for curiosity even when life feels busy.',
        ];

        for ($i = 0; $i < 15; $i++) {
            $text = $samples[$i % count($samples)] . ' #' . $i;
            $createdAt = now()->subDays(rand(1, 10))->subHours(rand(1, 23));
            Post::create([
                'user_id' => $user->id,
                'text' => $text,
                'image_url' => $i % 3 === 0 ? 'https://picsum.photos/seed/' . $i . '/800/600' : null,
                'embedding' => $this->mockEmbedding($text),
                'authenticity_score' => round(0.5 + ($i % 5) * 0.1, 4),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }
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
