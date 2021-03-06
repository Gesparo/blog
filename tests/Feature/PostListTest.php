<?php

namespace Tests\Unit;

use App\Post;
use App\Trending;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostListTest extends TestCase
{
    use RefreshDatabase;

    /** @var Trending */
    protected $trending;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->trending = new Trending();
        $this->trending->reset();
    }

    /** @test */
    public function if_posts_does_not_rated_it_will_return_empty_responce()
    {
        create(Post::class, [], 3);

        $response = $this->getJson(route('post.index'))
            ->assertStatus(200)
            ->json();

        $this->assertCount(0, $response);
    }

    /** @test */
    public function it_should_return_post_order_by_avg_rating()
    {
        $post1 = create(Post::class);
        $this->postJson(route('ratable.store'), ['post_id' => $post1->id, 'rating' => 4]);
        $post2 = create(Post::class);
        $this->postJson(route('ratable.store'), ['post_id' => $post2->id, 'rating' => 5]);
        $post3 = create(Post::class);
        $this->postJson(route('ratable.store'), ['post_id' => $post3->id, 'rating' => 1]);
        $post4 = create(Post::class);
        $this->postJson(route('ratable.store'), ['post_id' => $post4->id, 'rating' => 3]);
        $post5 = create(Post::class);
        $this->postJson(route('ratable.store'), ['post_id' => $post5->id, 'rating' => 2]);

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
        create(Post::class, [], 20)->each(function(Post $post) {
            $this->postJson(route('ratable.store'), ['post_id' => $post->id, 'rating' => random_int(1, 5)]);
        });

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
        create(Post::class, [], 20)->each(function(Post $post) {
            $this->postJson(route('ratable.store'), ['post_id' => $post->id, 'rating' => random_int(1, 5)]);
        });;

        $response = $this->getJson(route('post.index', ['limit' => 'invalid']))
            ->assertStatus(200)
            ->json();

        $this->assertCount(20, $response);
    }
}
