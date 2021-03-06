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
        $line = str_repeat('-', 131);

        echo PHP_EOL, '~> START [phpZM]', ' ';
        echo PHP_EOL, '   ', static::options('environment');
        echo PHP_EOL, '   ', static::options('url');
        echo PHP_EOL;
        echo $line, PHP_EOL;

        $client = new Client(static::options('url'), static::options('defaults'));

        $tests = 0;
        $error = 0;

        $asserts = static::options('tests');
        foreach ($asserts as $test) {
            /** @var Test $instance */
            $instance = new $test();
            $results = $instance->run($client);

            echo PHP_EOL, $test, PHP_EOL;
            echo $line, PHP_EOL;

            foreach ($results as $result) {
                $tests++;
                $status = $result['assert'];
                if (!$status) {
                    $error++;
                }

                printf("| %5s | %-5s | %-10s | %-10s | %-20s | %-60s |\n",
                    status($status),
                    $result['status'],
                        $result['method'],
                        $result['time'] . 'ms',
                        $result['name'],
                        $result['endpoint']
                );
            }

            $log = $this->logger($test);
            $this->log(static::options('root') . '/' . $log, $results);
            echo $line, PHP_EOL;
            printf("| %-127s |\n", $log);
            echo $line, PHP_EOL, PHP_EOL;
        }

        echo '~> ', 'RESUME', PHP_EOL;
        echo '   ', $tests, '/', $error, PHP_EOL, PHP_EOL;
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