<?php

// ============================================================
// This file contains all migration schemas for reference.
// In production, each migration is a separate file.
// Run: php artisan make:migration create_[table]_table
// ============================================================

// ─── 2024_01_01_000001_create_projects_table ─────────────────

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('client_name')->nullable();
            $table->string('project_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();           // array of image paths
            $table->json('tech_stack')->nullable();        // array of tech names
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('likes_count')->default(0);
            $table->date('project_date')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'published_at']);
            $table->index(['is_featured', 'sort_order']);
        });

        Schema::create('project_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->timestamps();
        });

        Schema::create('project_category', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'project_category_id']);
        });

        Schema::create('project_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('ip', 45);
            $table->timestamps();
            $table->unique(['project_id', 'ip']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_likes');
        Schema::dropIfExists('project_category');
        Schema::dropIfExists('project_categories');
        Schema::dropIfExists('projects');
    }
};

// ─── 2024_01_01_000002_create_posts_table ────────────────────

// Schema::create('posts', function (Blueprint $table) {
//     $table->id();
//     $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // author
//     $table->string('title');
//     $table->string('slug')->unique()->index();
//     $table->text('excerpt')->nullable();
//     $table->longText('content');
//     $table->string('cover_image')->nullable();
//     $table->string('seo_title')->nullable();
//     $table->text('seo_description')->nullable();
//     $table->string('og_image')->nullable();
//     $table->string('status')->default('draft'); // draft, published, scheduled
//     $table->boolean('is_featured')->default(false);
//     $table->unsignedInteger('views_count')->default(0);
//     $table->integer('reading_time')->nullable();   // minutes
//     $table->timestamp('published_at')->nullable();
//     $table->timestamps();
//     $table->softDeletes();
//     $table->index(['status', 'published_at']);
// });
//
// Schema::create('post_categories', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->string('slug')->unique()->index();
//     $table->string('description')->nullable();
//     $table->string('color', 7)->nullable();  // hex color
//     $table->timestamps();
// });
//
// Schema::create('post_tags', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->string('slug')->unique()->index();
//     $table->timestamps();
// });
//
// Schema::create('post_category', function (Blueprint $table) {
//     $table->foreignId('post_id')->constrained()->cascadeOnDelete();
//     $table->foreignId('post_category_id')->constrained()->cascadeOnDelete();
//     $table->primary(['post_id', 'post_category_id']);
// });
//
// Schema::create('post_tag', function (Blueprint $table) {
//     $table->foreignId('post_id')->constrained()->cascadeOnDelete();
//     $table->foreignId('post_tag_id')->constrained()->cascadeOnDelete();
//     $table->primary(['post_id', 'post_tag_id']);
// });

// ─── 2024_01_01_000003_create_contacts_table ─────────────────

// Schema::create('contacts', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->string('email');
//     $table->string('phone')->nullable();
//     $table->string('subject')->nullable();
//     $table->text('message');
//     $table->string('source')->default('website');
//     $table->string('status')->default('unread'); // unread, read, replied, archived
//     $table->string('ip', 45)->nullable();
//     $table->text('ua')->nullable();
//     $table->timestamp('read_at')->nullable();
//     $table->timestamps();
//     $table->index('status');
//     $table->index('created_at');
// });

// ─── 2024_01_01_000004_create_settings_table ─────────────────

// Schema::create('settings', function (Blueprint $table) {
//     $table->id();
//     $table->string('group')->default('general');  // general, seo, social, pricing
//     $table->string('key')->index();
//     $table->text('value')->nullable();
//     $table->string('type')->default('string');    // string, json, boolean, integer
//     $table->timestamps();
//     $table->unique(['group', 'key']);
// });

// ─── 2024_01_01_000005_create_services_table ─────────────────

// Schema::create('services', function (Blueprint $table) {
//     $table->id();
//     $table->string('title');
//     $table->string('slug')->unique();
//     $table->text('excerpt')->nullable();
//     $table->longText('content')->nullable();
//     $table->text('icon_svg')->nullable();
//     $table->boolean('is_active')->default(true);
//     $table->integer('sort_order')->default(0);
//     $table->timestamps();
// });

// ─── 2024_01_01_000006_create_clients_table ──────────────────

// Schema::create('client_logos', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->string('logo')->nullable();
//     $table->string('url')->nullable();
//     $table->boolean('is_active')->default(true);
//     $table->integer('sort_order')->default(0);
//     $table->timestamps();
// });

// ─── 2024_01_01_000007_create_pricing_plans_table ────────────

// Schema::create('pricing_plans', function (Blueprint $table) {
//     $table->id();
//     $table->string('name');
//     $table->string('slug')->unique();
//     $table->text('description')->nullable();
//     $table->decimal('price', 12, 2)->default(0);
//     $table->string('price_formatted')->nullable();
//     $table->json('features')->nullable();
//     $table->boolean('is_featured')->default(false);
//     $table->boolean('is_active')->default(true);
//     $table->integer('sort_order')->default(0);
//     $table->timestamps();
// });

// ─── 2024_01_01_000008_create_faqs_table ─────────────────────

// Schema::create('faqs', function (Blueprint $table) {
//     $table->id();
//     $table->string('question');
//     $table->text('answer');
//     $table->boolean('is_active')->default(true);
//     $table->integer('sort_order')->default(0);
//     $table->timestamps();
// });

// ─── 2024_01_01_000009_create_analytics_table ────────────────

// Schema::create('page_views', function (Blueprint $table) {
//     $table->id();
//     $table->string('path');
//     $table->string('ip', 45)->nullable();
//     $table->string('ua')->nullable();
//     $table->string('referrer')->nullable();
//     $table->string('country', 2)->nullable();
//     $table->timestamps();
//     $table->index('path');
//     $table->index('created_at');
// });
