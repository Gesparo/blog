<?php

namespace Tests\Unit;

use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_return_posts()
    {
        create(Post::class, [], 3);

        $response = $this->getJson(route('post.index'))
            ->assertStatus(200)
            ->json();

        $this->assertCount(3, $response);
    }

    /** @test */
    public function it_should_return_post_order_by_avg_rating()
    {
        $post1 = create(Post::class, ['avg_rating' => 4]);
        $post2 = create(Post::class, ['avg_rating' => 5]);
        $post3 = create(Post::class, ['avg_rating' => 1]);
        $post4 = create(Post::class, ['avg_rating' => 3]);
        $post5 = create(Post::class, ['avg_rating' => 2]);

        $response = $this->getJson(route('post.index'))
            ->assertStatus(200)
            ->json();

        $this->assertCount(5, $response);
        $this->assertEquals(
            [
                $post2->title,
                $post1->title,
                $post4->title,
                $post5->title,
                $post3->title,
            ],
            [
                $response[0]['title'],
                $response[1]['title'],
                $response[2]['title'],
                $response[3]['title'],
                $response[4]['title'],
            ]
        );
    }

    /** @test */
    public function we_can_set_limit_of_results()
    {
        create(Post::class, [], 20);

        $response = $this->getJson(route('post.index', ['limit' => 5]))
            ->assertStatus(200)
            ->json();

        $this->assertCount(5, $response);

        $response = $this->getJson(route('post.index', ['limit' => 10]))
            ->assertStatus(200)
            ->json();

        $this->assertCount(10, $response);
    }

    /** @test */
    public function limit_should_be_valid_if_not_use_default()
    {
        create(Post::class, [], 20);

        $response = $this->getJson(route('post.index', ['limit' => 'invalid']))
            ->assertStatus(200)
            ->json();

        $this->assertCount(20, $response);
    }
}
