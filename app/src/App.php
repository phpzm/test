<?php

namespace Testit;

use Testit\Http\Client;
use Testit\Scope\Test;

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
            'domain' => '',
            'url' => '',
            'tests' => [],
            'headers' => [],
            'cookies' => [],
        ];
        static::$options = array_merge($default, $options);
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
     * @SuppressWarnings("Unused")
     */
    public function run($arguments = [])
    {
        $asserts = static::option('tests');
        $tests = [];

        $client = new Client();
        foreach ($asserts as $test) {
            /** @var Test $instance */
            $instance = new $test();
            $result = $instance->run($client);
            $tests[$test] = $result;
        }

        foreach ($tests as $class => $results) {
            echo $class, PHP_EOL;
            foreach ($results as $name => $result) {
                echo status($result['status']), ' ', $name, ' ', '[', $result['endpoint'], ']', PHP_EOL;
            }
            echo '-----------------------------------------', PHP_EOL;
        }
    }
}