<?php

if (!function_exists('dd')) {
    function dd(...$param)
    {
        foreach ($param as $p)  {
            var_dump($p);
        }
        exit(1);
    }
}
if (!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);

        if ($value === false) {
            return value($default);
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;

            case 'false':
            case '(false)':
                return false;

            case 'empty':
            case '(empty)':
                return '';

            case 'null':
            case '(null)':
                return;
        }

        if (('"' === substr($value, 0, 1)) && ('"' === substr($value, -1, 1))) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}


//value()
if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}