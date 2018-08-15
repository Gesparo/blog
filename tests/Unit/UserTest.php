<?php

namespace Tests\Unit;

use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_have_many_posts()
    {
        $user = create(User::class);
        $posts = create(Post::class, ['user_id' => $user->id], 2);

        $this->assertEquals(2, $user->posts()->count());
    }
}
