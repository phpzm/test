<?php

namespace Testit;

use Testit\Error\ErrorDirectoryNotFound;
use DirectoryIterator;

/**
 * Class App
 * @package Testit
 */
class App
{
    /**
     * Options of App
     * @var array
     */
    private static $options = [];

    /**
     * App constructor.
     */
    public function __construct($options)
    {
        $default = [
            'root' => dirname(__DIR__, 2) . '/tests',
            'url' => ''
        ];
        static::$options = array_merge($options, $default);
    }

    /**
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    public static function option($option, $default = null)
    {
        if (!isset(static::$options[$option])) {
            return $default;
        }
        return static::$options[$option];
    }

    /**
     * @param array $arguments
     * @throws ErrorDirectoryNotFound
     */
    public function run($arguments)
    {
        $root = static::option('root');
        if (!file_exists($root)) {
            throw new ErrorDirectoryNotFound("Root `{$root}` not found");
        }
        foreach (new DirectoryIterator($root) as $item) {
            if ($item->isDot()) {
                continue;
            }
        }
    }
}