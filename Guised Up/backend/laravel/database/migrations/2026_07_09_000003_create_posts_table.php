<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('text');
            $table->string('image_url')->nullable();
            if (Schema::getConnection()->getDriverName() === 'pgsql') {
                $table->vector('embedding', 384);
            } else {
                // Fallback to JSON text column for SQLite/local testing
                $table->text('embedding')->nullable();
            }
            $table->float('authenticity_score');
            $table->timestamps();

            $table->index('created_at');
        });

        if (Schema::getConnection()->getDriverName() === 'pgsql') {
            DB::statement('CREATE INDEX IF NOT EXISTS posts_embedding_ivfflat_idx ON posts USING ivfflat (embedding vector_cosine_ops) WITH (lists = 100)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
