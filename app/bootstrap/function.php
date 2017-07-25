<?php

/**
 * @param bool $status
 * @return string
 */
function status($status)
{
    return $status ? success('success') : fail('fail   ');
}

/**
 * @return bool
 * @SuppressWarnings("SuperGlobals")
 */
function is_http()
{
    return isset($_SERVER['HTTP_HOST']);
}

/**
 * @param string $message
 * @return string
 */
function success($message)
{
    //chr(27) . '[42m success ' . chr(27) . '[0m'
    $success = chr(27) . '[42m' . $message . chr(27) . '[0m';
    if (is_http()) {
        $success = '<span style="background-color: green; color: #fff; padding: 0 5px;">' . $message . '</span>';
    }
    return $success;
}

/**
 * @param string $message
 * @return string
 */
function fail($message)
{
    //chr(27) . '[42m success ' . chr(27) . '[0m'
    $success = chr(27) . '[41m' . $message . chr(27) . '[0m';
    if (is_http()) {
        $success = '<span style="background-color: red; color: #fff; padding: 0 5px;">' . $message . '</span>';
    }
    return $success;
}