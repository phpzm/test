<?php

namespace Testit;

use Testit\Http\Client;
use Testit\Error\ErrorDirectoryNotFound;
use DirectoryIterator;
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
     * @throws ErrorDirectoryNotFound
     */
    public function run($arguments)
    {
        $root = static::option('root');
        if (!file_exists($root)) {
            throw new ErrorDirectoryNotFound("Root `{$root}` not found");
        }
        if ($arguments) {
            // TODO: resolve filters on run
        }
        $client = new Client();
        foreach (new DirectoryIterator($root) as $item) {
            if ($item->isDot()) {
                continue;
            }
            echo "test {$item->getFilename()}", PHP_EOL;

            /** @noinspection PhpIncludeInspection */
            require $item->getRealPath();

            $className = explode('.', $item->getFilename())[0];

            /** @var Test $instance */
            $instance = new $className();
            $result = $instance->run($client);
            echo json_encode($result), PHP_EOL;
        }
    }
}