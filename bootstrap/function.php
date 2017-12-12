<?php

if (!function_exists('test_status')) {
    /**
     * @param bool $status
     * @return string
     */
    function test_status($status)
    {
        return $status ? test_success('success') : test_fail('fail   ');
    }
}

/**
 * @SuppressWarnings("SuperGlobals")
 */
if (!function_exists('test_is_http')) {
    /**
     * @return bool
     */
    function test_is_http()
    {
        return isset($_SERVER['HTTP_HOST']);
    }
}

if (!function_exists('test_success')) {
    /**
     * @param string $message
     * @return string
     */
    function test_success($message)
    {
        //chr(27) . '[42m success ' . chr(27) . '[0m'
        $success = chr(27) . '[42m' . $message . chr(27) . '[0m';
        if (test_is_http()) {
            $success = '<span style="background-color: green; color: #fff; padding: 0 5px;">' . $message . '</span>';
        }
        return $success;
    }
}

if (!function_exists('test_fail')) {
    /**
     * @param string $message
     * @return string
     */
    function test_fail($message)
    {
        //chr(27) . '[42m success ' . chr(27) . '[0m'
        $success = chr(27) . '[41m' . $message . chr(27) . '[0m';
        if (test_is_http()) {
            $success = '<span style="background-color: red; color: #fff; padding: 0 5px;">' . $message . '</span>';
        }
        return $success;
    }
}
