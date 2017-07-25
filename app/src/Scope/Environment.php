<?php

namespace Testit\Scope;

use Simples\Error\SimplesRunTimeError;
use Simples\Helper\File;
use Simples\Helper\JSON;
use Testit\App;

/**
 * Class Environment
 * @package Testit\Scope
 */
abstract class Environment
{
    /**
     * @var string
     */
    public static $source = '';

    /**
     * @var string
     */
    public static $filename = 'environments.json';

    /**
     * Options of App
     * @var array
     */
    private static $environment = [];

    /**
     * @param string $index
     * @param bool $force
     * @return mixed|null
     * @throws SimplesRunTimeError
     */
    public static function get(string $index, $force = false)
    {
        if (!static::$environment || $force) {
            static::read();
        }
        return isset(static::$environment[$index]) ? static::$environment[$index] : null;
    }

    /**
     * @param string $index
     * @param string $value
     */
    public static function set(string $index, string $value)
    {
        static::$environment[$index] = $value;

        static::write();
    }

    /**
     * @return bool
     * @throws SimplesRunTimeError
     */
    public static function read()
    {
        $filename = App::option('root') . '/' . static::$filename;

        $entries = static::filter(static::environments($filename));

        foreach ($entries as $entry) {
            static::$environment[$entry->key] = $entry->value;
        }

        return !!count($entries);
    }

    /**
     * @return bool
     * @throws SimplesRunTimeError
     */
    private static function write()
    {
        $filename = App::option('root') . '/' . static::$filename;

        $environments = static::environments($filename);

        $index = 0;
        $entries = static::filter($environments, $index);

        foreach ($entries as $key => $entry) {
            if (isset(static::$environment[$entry->key])) {
                $entries[$key] = static::$environment[$entry->key];
            }
        }
        $environments[$index]->values = $entries;

        return !!File::write($filename, JSON::encode($environments, JSON_PRETTY_PRINT));
    }

    /**
     * @param $filename
     * @return array
     * @throws SimplesRunTimeError
     */
    private static function environments($filename)
    {
        $read = File::read($filename);
        if (!$read) {
            throw new SimplesRunTimeError("The file `{$filename}` not exists");
        }

        $environments = JSON::decode($read);
        if (!is_array($environments)) {
            throw new SimplesRunTimeError("The file `{$filename}` is invalid");
        }
        return $environments;
    }

    /**
     * @param array $environments
     * @param int $index
     * @return array
     */
    private static function filter(array $environments, int &$index = 0)
    {
        if (!static::$source) {
            static::$source = App::option('environment');
        }
        $source = static::$source;

        foreach ($environments as $key => $environment) {
            $index = $key;
            if ($environment->name === $source) {
                return $environment->values;
            }
        }
        return [];
    }
}