<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 17.08.2018
 * Time: 21:01.
 */

namespace App\Generator;

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Collection;
use App\Generator\Sender\DataSender;

class Generator
{
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
     * @var DataSender
     */
    private $postSender;
    /**
     * @var DataSender
     */
    private $ratingSender;

    /**
     * Generator constructor.
     *
     * You should create object using generator Builder
     *
     * @param Faker $faker
     * @param VisualizationInterface $visualizer
     * @param Timer $timer
     * @param DataSender $postSender
     * @param DataSender $ratingSender
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
        DataSender $postSender,
        DataSender $ratingSender,
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
        $this->postSender = $postSender;
        $this->ratingSender = $ratingSender;
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

                $this->ratingSender->send($posts->get($i), $ratingValue);

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

            $response = $this->postSender->send($users->random(), $ips->random());

            $result->push($response);

            if ($this->shouldVisualize) {
                $this->visualizer->showPostResponseInfo($response, $this->timer->getDiff());
            }
        }

        return $result;
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
