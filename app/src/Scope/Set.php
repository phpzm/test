<?php

namespace Testit\Scope;

/**
 * Class Set
 * @package Testit\Scope
 */
class Set
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Set constructor.
     * [
     *      [ ] TODO
     *      before => callable(string $method, string $endpoint, $body) ~> return $body,
     *      [x] TODO
     *      body => array ~> ['field' => 'value'] or ['field' => ['request' => 'A', 'response' => 'B']],
     *      [ ] TODO
     *      validation => callable(string $method, string $endpoint, $body, ResponseInterface $response) ~> return bool,
     *      [ ] TODO
     *      after => callable(string $method, string $endpoint, $body) ~> return $body,
     *
     * ]
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param $options
     * @return static
     */
    public static function make($options)
    {
        return new static($options);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (count($arguments)) {
            return $this->options[$name] = count($arguments) === 1 ? $arguments[0] : $arguments;
        }
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
        return null;
    }
}
