<?php

namespace Simples\Test;

use Simples\Helper\File;
use Simples\Helper\JSON;
use Simples\Helper\Text;
use Simples\Kernel\Base;
use Simples\Test\Http\Client;
use Simples\Test\Scope\Test;

/**
 * Class App
 * @package Testit
 */
class App extends Base
{
    /**
     * Default properties of options
     *
     * @var array
     */
    protected static $default = [
        'root' => '',
        'environment' => '',
        'domain' => '',
        'logs' => 'tests/logs',
        'url' => '',
        'tests' => [],
        'defaults' => [],
        'cookies' => [],
    ];

    /**
     * App constructor
     *
     * Create a instance of App Handler
     *
     * @param array $options ([
     *      'root' => string,
     *      'lang' => array,
     *      'labels' => boolean,
     *      'headers' => array,
     *      'type' => string
     *      'separator' => string
     *      'filter' => string,
     *      'avoid' => integer,
     *      'strict' => boolean
     *  ])
     */
    public function __construct($options)
    {
        static::setup($options);
    }

    /**
     * @param string $filename
     * @param array $results
     * @return bool
     */
    public function log(string $filename, array $results): bool
    {
        return !!File::write($filename, JSON::encode($results, JSON_PRETTY_PRINT));
    }

    /**
     * @param array $arguments
     * @SuppressWarnings("Unused")
     */
    public function run($arguments = [])
    {
        $asserts = static::options('tests');
        $tests = [];

        $client = new Client(static::options('url'), static::options('defaults'));

        foreach ($asserts as $test) {
            /** @var Test $instance */
            $instance = new $test();
            $results = $instance->run($client);
            $tests[$test] = $results;
        }

        echo PHP_EOL, '~> START [phpZM]', ' ';
        echo PHP_EOL, '   ', static::options('environment');
        echo PHP_EOL, '   ', static::options('url');
        echo PHP_EOL;
        echo '-----------------------------------------', PHP_EOL;

        foreach ($tests as $class => $results) {
            echo $class, PHP_EOL;
            foreach ($results as $name => $result) {
                echo status($result['assert']), ' ', $result['status'], ' ', $result['method'], ' ', $name, ' ', '[', $result['endpoint'], ']', PHP_EOL;
            }
            $log = $this->logger($class);
            $this->log(static::options('root') . '/' . $log, $results);
            echo '~> ', $log, PHP_EOL;
            echo '-----------------------------------------', PHP_EOL;
        }
    }

    /**
     * @param string $class
     * @return string
     */
    private function logger(string $class): string
    {
        return static::options('logs') . '/' . Text::replace($class, '\\', '/') . '.json';
    }
}