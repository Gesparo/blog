<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 19.08.2018
 * Time: 10:48.
 */

namespace App\Generator\Sender;

use App\Post;
use Ixudra\Curl\Facades\Curl;

class PostSender extends DataSender
{
    /**
     * Emulation of sending data to route.
     *
     * @param mixed ...$args
     * @return mixed
     */
    protected function sendFake(...$args)
    {
        [$user, $ip] = $args;

        return create(Post::class, ['user_id' => $user->id, 'user_ip' => $ip]);
    }

    /**
     * Send data to route.
     *
     * @param mixed ...$args
     * @return mixed
     */
    protected function sendRoute(...$args)
    {
        [$user, $ip] = $args;

        $requestData = array_merge(
            make(Post::class, ['user_id' => null, 'user_ip' => $ip])->toArray(),
            ['login' => $user->login]
        );

        return Curl::to(route('post.store'))
            ->withData($requestData)
            ->asJson()
            ->post();
    }
}
