<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BlogManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_editor_can_create_publish_and_edit_a_blog_post(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'is_active' => true,
            'admin_role' => 'content_editor',
            'admin_permissions' => ['content.manage'],
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($admin)->get('/admin/blogs')->assertOk()->assertSee('Blog posts');

        $this->actingAs($admin)->post('/admin/blogs', [
            'title' => 'A practical guide to choosing your course',
            'category' => 'Career planning',
            'excerpt' => 'A short guide for international students.',
            'image' => 'assets/example.jpg',
            'published_at' => '2026-08-29',
            'read_time' => '4 min read',
            'author' => 'GEIC team',
            'intro' => 'Start with the outcome you want.',
            'sections' => [['title' => 'Make a shortlist', 'copy' => 'Compare your options.']],
            'tags' => ['Courses', 'Planning'],
            'is_featured' => '1',
        ])->assertRedirect(route('admin.blogs.index'));

        $post = BlogPost::query()->where('title', 'A practical guide to choosing your course')->firstOrFail();
        $this->assertSame('draft', $post->status);
        $this->assertSame('a-practical-guide-to-choosing-your-course', $post->slug);

        $this->actingAs($admin)->post(route('admin.blogs.publish', $post))->assertRedirect();
        $this->assertSame('published', $post->fresh()->status);

        $this->actingAs($admin)->put(route('admin.blogs.update', $post), [
            'title' => 'An updated course guide',
            'category' => 'Career planning',
            'slug' => 'updated-course-guide',
            'excerpt' => 'Updated excerpt.',
            'read_time' => '3 min read',
            'author' => 'GEIC team',
            'intro' => 'Updated introduction.',
            'sections' => [],
            'tags' => [],
        ])->assertRedirect();

        $this->get('/blog/updated-course-guide')->assertOk()->assertSee('An updated course guide');
        $this->assertDatabaseMissing('blog_posts', ['slug' => 'a-practical-guide-to-choosing-your-course']);
    }
}
