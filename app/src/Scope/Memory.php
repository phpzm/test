<?php

namespace Testit\Scope;

/**
 * Class Memory
 * @package Testit\Scope
 */
abstract class Memory
{
    /**
     * Bag of values in memory
     * @var array
     */
    private static $bag = [];

    /**
     * @param string $index
     * @param mixed $value
     */
    public static function push(string $index, $value)
    {
        static::$bag[$index] = $value;
    }

    /**
     * @param string $index
     * @return mixed
     */
    public static function pull(string $index)
    {
        return isset(static::$bag[$index]) ? static::$bag[$index] : null;
    }

    /**
     * @return array
     */
    public static function all()
    {
        return static::$bag;
    }
}