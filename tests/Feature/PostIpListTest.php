<?php

namespace Tests\Unit;

use App\Post;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostIpListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_should_return_list_of_users_that_attach_user_ip()
    {
        /** @var Collection $users */
        $users = create(User::class, [], 5);

        $users->each(function (User $user) {
            create(Post::class, ['user_id' => $user->id, 'user_ip' => '127.0.0.0']);
        });

        $response = $this->getJson(route('ip.index'))
            ->assertStatus(200)
            ->json();

        $this->assertNotEmpty($response);
        $this->assertEquals('127.0.0.0', $response[0]['ip']);
        $this->assertEquals($users->pluck('login')->toArray(), $response[0]['users']);
    }

    /** @test */
    public function it_should_have_empty_response_if_there_is_no_posts()
    {
        $response = $this->getJson(route('ip.index'))
            ->assertStatus(200)
            ->json();

        $this->assertEmpty($response);
    }

    /** @test */
    public function it_should_return_few_ids_if_posts_have_multiple_user_ips()
    {
        /** @var Collection $users */
        $users = create(User::class, [], 5);

        $users->each(function (User $user) {
            create(Post::class, ['user_id' => $user->id, 'user_ip' => '127.0.0.0']);
        });
        $users->each(function (User $user) {
            create(Post::class, ['user_id' => $user->id, 'user_ip' => '127.1.1.1']);
        });

        $response = $this->getJson(route('ip.index'))
            ->assertStatus(200)
            ->json();

        $this->assertNotEmpty($response);
        $this->assertEquals('127.0.0.0', $response[0]['ip']);
        $this->assertEquals($users->pluck('login')->toArray(), $response[0]['users']);
        $this->assertEquals('127.1.1.1', $response[1]['ip']);
        $this->assertEquals($users->pluck('login')->toArray(), $response[1]['users']);
    }

    /** @test */
    public function it_should_return_one_user_user_posted_twice_from_the_same_ip()
    {
        /** @var Collection $user */
        $user = create(User::class, []);
        create(Post::class, ['user_id' => $user->id, 'user_ip' => '127.0.0.0']);
        create(Post::class, ['user_id' => $user->id, 'user_ip' => '127.0.0.0']);

        $response = $this->getJson(route('ip.index'))
            ->assertStatus(200)
            ->json();

        $this->assertNotEmpty($response);
        $this->assertCount(1, $response);
    }
}
