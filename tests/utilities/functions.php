<?php
/**
 * Created by PhpStorm.
 * User: gesparo
 * Date: 30.05.2018
 * Time: 21:25
 */

function create($class, $attributes = [], $amount = null)
{
    return factory($class, $amount)->create($attributes);
}

function make($class, $attributes = [], $amount = null)
{
    return factory($class, $amount)->make($attributes);
}