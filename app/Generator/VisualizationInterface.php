<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 17.08.2018
 * Time: 21:48
 */

namespace App\Generator;

/**
 * Interface VisualizationInterface
 * @package App\Generator
 */
interface VisualizationInterface
{
    /**
     * Show post title
     *
     * @return string
     */
    public function showPostTitle() :void;

    /**
     * Show post response information
     *
     * @param $postInfo
     * @param $timing
     * @return void
     */
    public function showPostResponseInfo($postInfo, $timing) :void;

    /**
     * Show rating title
     *
     * @return string
     */
    public function showRatingTitle() :void;

    /**
     * Show rating responce information
     *
     * @param $ratingInfo
     * @param $timing
     * @return void
     */
    public function showRatingResponseInfo($ratingInfo, $timing) :void;
}