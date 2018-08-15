<?php

namespace Tests\Unit;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostCreationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function anyone_can_create_post()
    {
        $post = make(Post::class, ['user_id' => null]);
        $user = make(User::class);

        $response = $this->postRequest($post->toArray(), ['login' => $user->login])
            ->assertStatus(200)
            ->json();

        $validResponse = ['title' => $post->title, 'body' => $post->body, 'user_ip' => $post->user_ip];

        $this->assertArraySubset($validResponse, $response);
        $this->assertDatabaseHas('posts', $validResponse);
    }

    /** @test */
    public function if_user_does_not_exsists_it_will_create_new_one()
    {
        $this->assertEquals(0, User::getQuery()->count());

        $this->postRequest(['user_id' => null]);

        $this->assertEquals(1, User::getQuery()->count());
    }

    /** @test */
    public function title_is_required()
    {
        $this->withExceptionHandling();

        $response = $this->postRequest(['user_id' => null, 'title' => null])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('title', $response['errors']);
    }

    /** @test */
    public function title_must_have_more_then_3_characters()
    {
        $this->withExceptionHandling();

        $response = $this->postRequest(['user_id' => null, 'title' => 'Sf'])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('title', $response['errors']);
    }

    /** @test */
    public function title_must_be_unique()
    {
        $this->withExceptionHandling();

        $post = make(Post::class);

        $this->postRequest($post->toArray())
            ->assertStatus(200);

        $response = $this->postRequest($post->toArray())
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('title', $response['errors']);
    }

    /** @test */
    public function body_is_required()
    {
        $this->withExceptionHandling();

        $response = $this->postRequest(['user_id' => null, 'body' => null])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('body', $response['errors']);
    }

    /** @test */
    public function body_must_have_more_then_3_characters()
    {
        $this->withExceptionHandling();

        $response = $this->postRequest(['user_id' => null, 'body' => 'sf'])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('body', $response['errors']);
    }

    /** @test */
    public function login_is_required()
    {
        $this->withExceptionHandling();

        $response = $this->postRequest(['user_id' => null], ['login' => null])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('login', $response['errors']);
    }

    /** @test */
    public function user_ip_is_required()
    {
        $this->withExceptionHandling();

        $response = $this->postRequest(['user_id' => null, 'user_ip' => null])
            ->assertStatus(422)
            ->json();

        $this->assertArrayHasKey('errors', $response);
        $this->assertArrayHasKey('user_ip', $response['errors']);
    }

    /**
     * Request to the server
     *
     * @param array $postAttributes
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function postRequest($postAttributes = [], $userAttributes = [])
    {
        $post = make(Post::class, $postAttributes);
        $user = make(User::class);

        $request = array_merge(
            $post->toArray(),
            empty($userAttributes) ? ['login' => $user->login] : $userAttributes);

        return $this->postJson(route('post.store'), $request);
    }
}
