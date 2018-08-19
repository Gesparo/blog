<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 19.08.2018
 * Time: 10:38
 */

namespace App\Generator\Sender;


abstract class DataSender
{
    /**
     * Is generator should send real requests to routes.
     *
     * @var bool
     */
    private static $isFake = false;

    /**
     * Disable sending requests to routes. Create in database immediately instead.
     *
     * @return bool
     */
    final public static function fake(): bool
    {
        return self::$isFake = true;
    }

    /**
     * Check if generator status is fake.
     *
     * @return bool
     */
    final public static function isFake(): bool
    {
        return self::$isFake;
    }

    /**
     * Send data
     *
     * @param mixed ...$args
     * @return mixed
     */
    public function send(...$args)
    {
        if( self::isFake() ) {
            return $this->sendFake($args);
        }

        return $this->sendRoute($args);
    }

    /**
     * Emulation of sending data to route
     *
     * @param mixed ...$args
     * @return mixed
     */
    abstract protected function sendFake(...$args);

    /**
     * Send data to route
     *
     * @param mixed ...$args
     * @return mixed
     */
    abstract protected function sendRoute(...$args);
}