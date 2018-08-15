<?php

namespace Tests\Unit;

use App\Post;
use App\Rating;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatablePostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_be_rated()
    {
        $post = create(Post::class);
        $rating = make(Rating::class, ['post_id' => $post->id]);

        $this->assertEquals(0, Rating::getQuery()->count());

        $this->postJson(route('ratable.store'), ['post_id' => $post->id, 'rating' => $rating->rating])
            ->assertStatus(200);

        $this->assertEquals(1, Rating::getQuery()->count());
        $this->assertDatabaseHas('ratings', ['post_id' => $post->id, 'rating' => $rating->rating]);
    }
    
    /** @test */
    public function it_should_return_average_value()
    {
        $post = create(Post::class);
        $rating = make(Rating::class, ['post_id' => $post->id]);

        $response = $this->postJson(route('ratable.store'), ['post_id' => $post->id, 'rating' => $rating->rating])
            ->assertStatus(200)
            ->json();

        $this->assertArraySubset(['average' => $rating->rating], $response);
    }

    /** @test */
    public function it_average_rating_should_be_correct_for_few_ratings()
    {
        $post = create(Post::class);
        /** @var Collection $ratings */
        $ratings = make(Rating::class, ['post_id' => $post->id], 3);

        $validAverage = round($ratings->pluck('rating')->avg(), 2);

        foreach ($ratings as $rating) {
            $response = $this->postJson(
                    route('ratable.store'), ['post_id' => $post->id, 'rating' => $rating->rating]
                )
                ->assertStatus(200)
                ->json();
        }

        $this->assertSame((double) $validAverage, (double) $response['average']);
    }

    /** @test */
    public function it_should_corrct_get_average_for_different_post()
    {
        create(Post::class, [], 2)->each(function(Post $post) {
            /** @var Collection $ratings */
            $ratings = make(Rating::class, ['post_id' => $post->id], 3);

            $validAverage = round($ratings->pluck('rating')->avg(), 2);

            foreach ($ratings as $rating) {
                $response = $this->postJson(
                    route('ratable.store'), ['post_id' => $post->id, 'rating' => $rating->rating]
                )
                    ->assertStatus(200)
                    ->json();
            }

            $this->assertSame((double) $validAverage, (double) $response['average']);
        });
    }

    /** @test */
    public function post_id_is_required()
    {
        $this->withExceptionHandling();

        $response = $this->postJson(route('ratable.store'), ['post_id' => null, 'rating' => 2])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('post_id', $response['errors']);
    }

    /** @test */
    public function post_id_must_be_is_of_created_post()
    {
        $this->withExceptionHandling();

        create(Post::class);
        $invalidId = 100;

        $response = $this->postJson(route('ratable.store'), ['post_id' => $invalidId, 'rating' => 2])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('post_id', $response['errors']);
    }
    
    /** @test */
    public function rating_is_required()
    {
        $this->withExceptionHandling();

        $post = create(Post::class);

        $response = $this->postJson(route('ratable.store'), ['post_id' => $post->id, 'rating' => null])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('rating', $response['errors']);
    }

    /** @test */
    public function rating_must_be_numeric()
    {
        $this->withExceptionHandling();

        $post = create(Post::class);

        $response = $this->postJson(route('ratable.store'), ['post_id' => $post->id, 'rating' => 'invalid'])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('rating', $response['errors']);
    }

    /**
     * @dataProvider ratingDataProvider
     * @test
     * @param $rating
     * @param $isValid
     */
    public function rating_value_must_be_between_1_and_5($rating, $isValid)
    {
        $this->withExceptionHandling();

        $post = create(Post::class);

        $response = $this->postJson(route('ratable.store'), ['post_id' => $post->id, 'rating' => $rating]);

        if( $isValid )
        {
            $response->assertStatus(200);
        }
        else
        {
            $response->assertStatus(422);

            $this->assertArrayHasKey('errors', $response->json());
            $this->assertArrayHasKey('rating', $response->json()['errors']);
        }
    }

    public function ratingDataProvider()
    {
        return [
            [-1, false],
            [0, false],
            [1, true],
            [2, true],
            [3, true],
            [4, true],
            [5, true],
            [6, false],
        ];
    }

    /** @test */
    public function after_adding_rating_it_should_update_avg_rating_in_post()
    {
        $post = create(Post::class);

        $this->assertSame(0.0, $post->avg_rating);

        $this->postJson(route('ratable.store'), ['post_id' => $post->id, 'rating' => 2]);

        $this->assertSame((double) 2, (double) $post->fresh()->avg_rating);
    }
}
