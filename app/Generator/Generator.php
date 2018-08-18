<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 17.08.2018
 * Time: 21:01.
 */

namespace App\Generator;

use App\Post;
use App\User;
use App\Rating;
use Faker\Generator as Faker;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Collection;

class Generator
{
    /**
     * Is generator should send real requests to routes.
     *
     * @var bool
     */
    private static $isFake = false;
    /**
     * @var int
     */
    private $usersAmount;
    /**
     * @var int
     */
    private $ipsAmount;
    /**
     * @var int
     */
    private $postAmount;
    /**
     * @var int
     */
    private $ratingForOnePostLimit;
    /**
     * @var bool
     */
    private $shouldVisualize;
    /**
     * @var Faker
     */
    private $faker;
    /**
     * Display information in output.
     *
     * @var VisualizationInterface
     */
    private $visualizer;
    /**
     * @var Timer
     */
    private $timer;
    /**
     * @var int
     */
    private $ratingsAmount;

    /**
     * Generator constructor.
     *
     * You should create object using generator Builder
     *
     * @param Faker $faker
     * @param VisualizationInterface $visualizer
     * @param Timer $timer
     * @param int $usersAmount
     * @param int $ipsAmount
     * @param int $postAmount
     * @param int $ratingsAmount
     * @param int $ratingForOnePostAmount
     * @param bool $shouldVisualize
     */
    public function __construct(
        Faker $faker,
        VisualizationInterface $visualizer,
        Timer $timer,
        $usersAmount = 100,
        $ipsAmount = 100,
        $postAmount = 100,
        $ratingsAmount = 50,
        $ratingForOnePostAmount = 50,
        $shouldVisualize = false
    ) {
        $this->usersAmount = $usersAmount;
        $this->ipsAmount = $ipsAmount;
        $this->postAmount = $postAmount;
        $this->ratingForOnePostLimit = $ratingForOnePostAmount;
        $this->shouldVisualize = $shouldVisualize;
        $this->faker = $faker;
        $this->visualizer = $visualizer;
        $this->timer = $timer;
        $this->ratingsAmount = $ratingsAmount;
    }

    /**
     * Disable sending requests to routes. Create in database immediately instead.
     *
     * @return bool
     */
    public static function fake(): bool
    {
        return self::$isFake = true;
    }

    /**
     * Check if generator status is fake.
     *
     * @return bool
     */
    public static function isFake(): bool
    {
        return self::$isFake;
    }

    /**
     * Start generator process.
     *
     * @return bool
     */
    public function start() :bool
    {
        $users = $this->createUsers();

        $ips = $this->generateIps();

        $this->setScriptTimeLimit();

        $posts = $this->addPosts($users, $ips);

        $this->addRatings($posts);

        return true;
    }

    /**
     * Set script limit time.
     *
     * @return bool
     */
    private function setScriptTimeLimit() :bool
    {
        $limit = $this->postAmount + $this->ratingsAmount * $this->ratingForOnePostLimit;

        // fix mistake when script time is too short
        return set_time_limit($limit < 10 ? 10 : $limit);
    }

    /**
     * Add rating for posts.
     *
     * @param Collection $posts
     * @return bool
     */
    private function addRatings(Collection $posts): bool
    {
        if ($this->shouldVisualize) {
            $this->visualizer->showRatingTitle();
        }

        $ratingAmount = $posts->count() < $this->ratingsAmount ?
            $posts->count() : $this->ratingsAmount;

        for ($i = 0; $i < $ratingAmount; $i++) {
            for ($j = 0; $j < $this->faker->numberBetween(1, $this->ratingForOnePostLimit); $j++) {
                $this->timer->start();

                $ratingValue = $this->faker->numberBetween(1, 5);

                if (self::$isFake) {
                    $this->addFakeRating($posts->get($i), $ratingValue);
                }

                $this->addRouteRating($posts->get($i), $ratingValue);

                if ($this->shouldVisualize) {
                    $this->visualizer->showRatingResponseInfo(
                        'Post: '.$posts->get($i)->id.', Iteration '.($j + 1),
                        $this->timer->getDiff()
                    );
                }
            }
        }

        return true;
    }

    /**
     * Send request to route and add new rating.
     *
     * @param $post
     * @param $rating
     * @return bool
     */
    private function addRouteRating($post, $rating): bool
    {
        Curl::to(route('ratable.store'))
            ->withData(['post_id' => $post->id, 'rating' => $rating])
            ->asJson()
            ->post();

        return true;
    }

    /**
     * Add rating without sending request to route.
     *
     * @param $post
     * @param $rating
     * @return bool
     */
    private function addFakeRating($post, $rating): bool
    {
        // it is not good idea to duplicate route and generator logic, but it is the simplest solution
        Rating::create(['post_id' => $post->id, 'rating' => $rating]);

        $average = round(
            Rating::where('post_id', $post->id)->avg('rating'),
            2);

        Post::where('id', $post->id)
            ->limit(1)
            ->update(['avg_rating' => $average]);

        return true;
    }

    /**
     * Add new posts.
     *
     * @param Collection $users
     * @param Collection $ips
     * @return Collection
     */
    private function addPosts(Collection $users, Collection $ips): Collection
    {
        if ($this->shouldVisualize) {
            $this->visualizer->showPostTitle();
        }

        $result = collect([]);

        for ($i = 0; $i < $this->postAmount; $i++) {
            $this->timer->start();

            if (self::$isFake) {
                $response = $this->addFakePost($users->random(), $ips->random());
            } else {
                $response = $this->addRoutePost($users->random(), $ips->random());
            }

            $result->push($response);

            if ($this->shouldVisualize) {
                $this->visualizer->showPostResponseInfo($response, $this->timer->getDiff());
            }
        }

        return $result;
    }

    /**
     * Create post using requests.
     *
     * @param $user
     * @param $ip
     * @return \stdClass
     */
    private function addRoutePost($user, $ip): \stdClass
    {
        $requestData = array_merge(
            make(Post::class, ['user_id' => null, 'user_ip' => $ip])->toArray(),
            ['login' => $user->login]
        );

        return Curl::to(route('post.store'))
            ->withData($requestData)
            ->asJson()
            ->post();
    }

    /**
     * Create posts without sending request to route.
     *
     * @param $user
     * @param $ip
     * @return Post
     */
    private function addFakePost($user, $ip): Post
    {
        return create(Post::class, ['user_id' => $user->id, 'user_ip' => $ip]);
    }

    /**
     * Create users.
     *
     * @return Collection
     */
    private function createUsers(): Collection
    {
        if (1 === $this->usersAmount) {
            return collect([create(User::class)]);
        }

        return create(User::class, [], $this->usersAmount);
    }

    /**
     * Generate ips.
     *
     * @return Collection
     */
    private function generateIps(): Collection
    {
        $result = collect([]);

        for ($i = 0; $i < $this->ipsAmount; $i++) {
            $result->push($this->faker->ipv4);
        }

        return $result;
    }
}
