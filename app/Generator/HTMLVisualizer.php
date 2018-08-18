<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 17.08.2018
 * Time: 21:54
 */

namespace App\Generator;

/**
 * Class HTMLVisualizer
 *
 * Visualize generator response in html
 *
 * @package App\Generator
 */
class HTMLVisualizer implements VisualizationInterface
{

    /**
     * Show post title
     *
     * @return void
     */
    public function showPostTitle(): void
    {
        echo '<h2>Add posts</h2>';
    }

    /**
     * Show post response information
     *
     * @param $postInfo
     * @param $timing
     * @return void
     */
    public function showPostResponseInfo($postInfo, $timing): void
    {
        echo $postInfo->id . ' | Time: ' . $timing . '<br>';
    }

    /**
     * Show rating title
     *
     * @return void
     */
    public function showRatingTitle(): void
    {
        echo '<h2>Add rating</h2>';
    }

    /**
     * Show rating responce information
     *
     * @param $ratingInfo
     * @param $timing
     * @return void
     */
    public function showRatingResponseInfo($ratingInfo, $timing): void
    {
        echo $ratingInfo . ' | Time: ' . $timing . '<br>';
    }
}