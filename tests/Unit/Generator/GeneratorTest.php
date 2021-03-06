<?php

namespace Tests\Unit;

use App\Generator\Builder;
use App\Generator\Generator;
use App\Generator\Sender\DataSender;
use App\Post;
use App\Rating;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Builder
     */
    protected $builder;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->builder = new Builder();
        DataSender::fake();
    }

    /** @test */
    public function it_can_create_users()
    {
        $generator = $this->builder
            ->limitUsers(2)
            ->limitIps(1)
            ->limitPosts(1)
            ->limitRating(1)
            ->limitRatingForOnePost(1)
            ->create();

        $this->assertSame(0, User::getQuery()->count());

        $generator->start();

        $this->assertSame(2, User::getQuery()->count());
    }

    /** @test */
    public function it_can_create_posts()
    {
        $generator = $this->builder
            ->limitUsers(1)
            ->limitIps(1)
            ->limitPosts(2)
            ->limitRating(1)
            ->limitRatingForOnePost(1)
            ->create();

        $this->assertSame(0, Post::getQuery()->count());

        $generator->start();

        $this->assertSame(2, Post::getQuery()->count());
    }

    /** @test */
    public function it_can_crete_different_ips()
    {
        $generator = $this->builder
            ->limitUsers(1)
            ->limitIps(2)
            ->limitPosts(10)
            ->limitRating(1)
            ->limitRatingForOnePost(1)
            ->create();

        $generator->start();

        $this->assertSame(2, Post::all(['user_ip'])->pluck('user_ip')->unique()->count());
    }

    /** @test */
    public function it_can_create_ratings()
    {
        $generator = $this->builder
            ->limitUsers(1)
            ->limitIps(1)
            ->limitPosts(2)
            ->limitRating(2)
            ->limitRatingForOnePost(1)
            ->create();

        $this->assertSame(0, Rating::getQuery()->count());

        $generator->start();

        $this->assertSame(2, Rating::getQuery()->count());
    }

    /** @test */
    public function it_can_create_more_then_one_rating_for_each_post()
    {
        $generator = $this->builder
            ->limitUsers(1)
            ->limitIps(1)
            ->limitPosts(1)
            ->limitRating(1)
            ->limitRatingForOnePost(2)
            ->create();

        $this->assertSame(0, Rating::getQuery()->count());

        $generator->start();

        $this->assertTrue(Rating::getQuery()->count() > 0 && Rating::getQuery()->count() <= 2);
    }
}
