<?php

/**
 * User: nixbpe
 * Date: 1/12/2016 AD
 * Time: 10:57 AM
 */

use Illuminate\Support\Str;

if (!function_exists('env_secure')) {

    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    function env_secure($key, $default = null)
    {

        $value = getenv($key);
        if ($value === false) {
            return value($default);
        }

        $env = env($key);

        if (str_contains($key, "SECURE")) {
            try {
                if (Str::startsWith($key = config('app.key'), 'base64:')) {
                    $key = base64_decode(substr($key, 7));
                }

                $encrypter = new \Illuminate\Encryption\Encrypter($key, config('app.cipher'));
                return $encrypter->decrypt($env);
            } catch (Exception $e) {
                return $env;
            }
        }

        return $env;
    }
}
