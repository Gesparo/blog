<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Illuminate\Http\Request;
use Faker\Generator as Faker;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Database\Eloquent\Collection;

class GeneratorController extends Controller
{
    public function index(Request $request, Faker $faker)
    {
        $visualization = (bool) $request->get('visualization', false);
        $limitOfPosts = (int) $request->get('posts_limit', 1000);
        $rangeOfRatings = (int) $request->get('rating_range', 500);

        // Add users

        $users = $this->createUsers();

        // Add ips
        $ips = $this->createIps($faker);

        // Add posts
        set_time_limit(1000);

        if ($visualization) {
            echo 'Add posts<br><br>';
        }

        for ($i = 0; $i < $limitOfPosts; $i++) {
            $time_start = microtime(true);

            $res = Curl::to(route('post.store'))
                ->withData(array_merge(
                    make(Post::class, ['user_id' => null, 'user_ip' => $ips->random()])->toArray(),
                    ['login' => $users->random()->login]
                ))
                ->asJson()
                ->post();

            $time_end = microtime(true);
            $time = $time_end - $time_start;

            if ($visualization) {
                echo $res->id.' | Time: '.$time.'<br>';
            }
        }

        if ($visualization) {
            echo 'Add rating<br><br>';
        }

        // Add rating for first 500 posts
        set_time_limit(1000);

        for ($i = 0; $i < $rangeOfRatings; $i++) {
            for ($j = 0; $j < $faker->numberBetween(10, 50); $j++) {
                $time_start = microtime(true);

                $res = Curl::to(route('ratable.store'))
                    ->withData(['post_id' => $i + 1, 'rating' => $faker->numberBetween(1, 5)])
                    ->asJson()
                    ->post();

                $time_end = microtime(true);
                $time = $time_end - $time_start;

                if ($visualization) {
                    echo '| Time: '.$time.'<br>';
                }
            }
        }
    }

    /**
     * @return Collection
     */
    private function createUsers(): Collection
    {
        return create(User::class, [], 100);
    }

    /**
     * @param Faker $faker
     * @return \Illuminate\Support\Collection
     */
    private function createIps(Faker $faker): \Illuminate\Support\Collection
    {
        $ips = collect([]);

        for ($i = 0; $i < 100; $i++) {
            $ips->push($faker->ipv4);
        }

        return $ips;
    }
}
