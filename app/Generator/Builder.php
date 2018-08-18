<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 17.08.2018
 * Time: 21:01.
 */

namespace App\Generator;

use Faker\Generator as Faker;
use InvalidArgumentException;

class Builder
{
    /**
     * @var int
     */
    private $usersAmount = 100;
    /**
     * @var int
     */
    private $ipsAmount = 100;
    /**
     * @var int
     */
    private $postAmount = 100;
    /**
     * @var int
     */
    private $ratingForOnePostLimit = 50;
    /**
     * @var bool
     */
    private $shouldVisualize = false;
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
    private $ratingsAmount = 50;

    /**
     * Builder constructor.
     */
    public function __construct()
    {
        $this->visualizer = new HTMLVisualizer();
        $this->faker = \Faker\Factory::create();
        $this->timer = new Timer();
    }

    /**
     * Set visualization interface.
     *
     * @param VisualizationInterface $visualizer
     * @return Builder
     */
    public function visualizationInstance(VisualizationInterface $visualizer): self
    {
        $this->visualizer = $visualizer;

        return $this;
    }

    /**
     * Set users limit.
     *
     * @param int $users
     * @return $this
     * @throws InvalidArgumentException
     */
    public function limitUsers(int $users = 100): self
    {
        if ($users < 1) {
            throw new InvalidArgumentException('Users limit must be positive.');
        }

        $this->usersAmount = $users;

        return $this;
    }

    /**
     * Set ips limit.
     *
     * @param int $ips
     * @return $this
     * @throws InvalidArgumentException
     */
    public function limitIps(int $ips = 100): self
    {
        if ($ips < 1) {
            throw new InvalidArgumentException('Ip limit must be positive.');
        }

        $this->ipsAmount = $ips;

        return $this;
    }

    /**
     * Set posts limit.
     *
     * @param int $posts
     * @return $this
     * @throws InvalidArgumentException
     */
    public function limitPosts(int $posts = 100): self
    {
        if ($posts < 1) {
            throw new InvalidArgumentException('Post limit must be positive.');
        }

        $this->postAmount = $posts;

        return $this;
    }

    /**
     * Set rating limit.
     *
     * @param int $rating
     * @return Builder
     * @throws InvalidArgumentException
     */
    public function limitRating(int $rating = 50) :self
    {
        if ($rating < 1) {
            throw new InvalidArgumentException('Rating limit must be positive.');
        }

        if ($rating > $this->postAmount) {
            throw new InvalidArgumentException('Rating must be less or equal post limit');
        }

        $this->ratingsAmount = $rating;

        return $this;
    }

    /**
     * Set rating limit for one post.
     *
     * @param int $rating
     * @return Builder
     */
    public function limitRatingForOnePost(int $rating = 50) :self
    {
        if ($rating < 1) {
            throw new InvalidArgumentException('Rating for one post must be positive');
        }

        $this->ratingForOnePostLimit = $rating;

        return $this;
    }

    /**
     * Display steps in output according to VisualizationInterface instance.
     *
     * @param bool $status
     * @return Builder
     */
    public function visualize(bool $status = true) :self
    {
        $this->shouldVisualize = $status;

        return $this;
    }

    /**
     * Create new instance of Generator.
     *
     * @return Generator
     */
    public function create() :Generator
    {
        return new Generator(
            $this->faker,
            $this->visualizer,
            $this->timer,
            $this->usersAmount,
            $this->ipsAmount,
            $this->postAmount,
            $this->ratingsAmount,
            $this->ratingForOnePostLimit,
            $this->shouldVisualize
        );
    }
}
