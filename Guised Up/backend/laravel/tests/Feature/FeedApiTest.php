<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_post_stores_an_embedding(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/posts', ['text' => 'A fresh post about thoughtful habits']);

        $response->assertCreated();
        $this->assertNotNull($response->json('id'));
        $this->assertCount(384, Post::first()->embedding);
    }

    public function test_feed_returns_paginated_ranked_results(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        Post::create(['user_id' => $user->id, 'text' => 'Own post', 'embedding' => array_fill(0, 384, 0.1), 'authenticity_score' => 0.9]);
        Post::create(['user_id' => $other->id, 'text' => 'Other post', 'embedding' => array_fill(0, 384, 0.2), 'authenticity_score' => 0.5]);

        $response = $this->getJson('/api/feed?page=1');

        $response->assertOk();
        $this->assertArrayHasKey('data', $response->json());
    }

    public function test_search_returns_semantically_relevant_results(): void
    {
        $user = User::factory()->create();
        Post::create(['user_id' => $user->id, 'text' => 'A thoughtful post about music and calm mornings', 'embedding' => array_fill(0, 384, 0.1), 'authenticity_score' => 0.8]);
        Post::create(['user_id' => $user->id, 'text' => 'A completely unrelated topic about kitchen appliances', 'embedding' => array_fill(0, 384, 0.2), 'authenticity_score' => 0.8]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/search?q=music');

        $response->assertOk();
        $this->assertNotEmpty($response->json());
    }
}
