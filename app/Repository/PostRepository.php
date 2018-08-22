<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 22.08.2018
 * Time: 17:14.
 */

namespace App\Repository;

use App\Post;
use App\Trending;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PostRepository
{
    /**
     * @var Trending
     */
    private $trending;

    /**
     * PostRepository constructor.
     * @param Request $request
     * @param Trending $trending
     */
    public function __construct(Trending $trending)
    {
        $this->trending = $trending;
    }

    /**
     * Get popular posts.
     *
     * @param int $limit
     * @return Collection
     */
    public function popular($limit = 50) :Collection
    {
        $trendingElements = $this->trending->get($limit);

        if ($trendingElements->isEmpty()) {
            return collect([]);
        }

        return $this->sortInSpecificOrder($trendingElements, Post::find($trendingElements))
            ->map(function (Post $post) {
                return collect($post->toArray())
                    ->only(['title', 'body'])
                    ->all();
            });
    }

    /**
     * Sort posts by trending.
     *
     * @param Collection $trendingOrder
     * @param Collection $posts
     * @return Collection
     */
    private function sortInSpecificOrder(Collection $trendingOrder, Collection $posts) :Collection
    {
        $result = collect([]);

        foreach ($trendingOrder as $trendingItem) {
            $targetPostPosition = $posts->search(function (Post $post) use ($trendingItem) {
                return $post->id === (int) $trendingItem;
            });

            if (false === $targetPostPosition) {
                continue;
            }

            $result->push($posts->get($targetPostPosition));
        }

        return $result;
    }
}
