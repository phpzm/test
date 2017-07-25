<?php

namespace Testit;

use Simples\Helper\File;
use Simples\Helper\JSON;
use Simples\Helper\Text;
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
            'root' => dirname(__DIR__, 2),
            'environment' => '',
            'domain' => '',
            'url' => '',
            'tests' => [],
            'defaults' => [],
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
     * @param string $class
     * @param array $results
     * @return bool
     */
    public function log(string $class, array $results): bool
    {
        $namespace = Text::replace($class, '\\', '/');
        $filename = static::option('root') . '/' . 'logs' . '/' . $namespace . '.json';
        return !!File::write($filename, JSON::encode($results, JSON_PRETTY_PRINT));
    }

    /**
     * @param array $arguments
     * @SuppressWarnings("Unused")
     */
    public function run($arguments = [])
    {
        $asserts = static::option('tests');
        $tests = [];

        $client = new Client(static::option('url'), static::option('defaults'));

        foreach ($asserts as $test) {
            /** @var Test $instance */
            $instance = new $test();
            $result = $instance->run($client);
            $tests[$test] = $result;
            $this->log($test, $result);
        }

        foreach ($tests as $class => $results) {
            echo $class, PHP_EOL;
            foreach ($results as $name => $result) {
                echo status($result['assert']), ' ', $result['status'], ' ', $result['method'], ' ', $name, ' ', '[', $result['endpoint'], ']', PHP_EOL;
            }
            echo '-----------------------------------------', PHP_EOL;
        }
    }
}