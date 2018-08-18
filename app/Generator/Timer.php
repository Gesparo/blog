<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 17.08.2018
 * Time: 22:07
 */

namespace App\Generator;

class Timer
{
    /**
     * Store start time
     *
     * @var int
     */
    private $time = 0;

    /**
     * Start timer
     *
     * @return int
     */
    public function start() :float
    {
        return $this->time = microtime(true);
    }

    /**
     * Get difference between current tile and start time
     *
     * @return int
     */
    public function getDiff() :float
    {
        $currentTime = microtime(true);

        return $currentTime - $this->time;
    }
}