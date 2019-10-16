<?php

/**
 * Class Validate
 */
class Validate
{
    /**
     * Check for date validity.
     *
     * @param string $date Date to validate
     *
     * @return bool Parameter is valid date or not
     */
    public static function isDate($date)
    {
        if (!preg_match('/^([0-9]{4})-((?:0?[0-9])|(?:1[0-2]))-((?:0?[0-9])|(?:[1-2][0-9])|(?:3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/', $date, $matches)) {
            return false;
        }

        return checkdate((int) $matches[2], (int) $matches[3], (int) $matches[1]);
    }

    /**
     * Check for an integer validity.
     *
     * @param int $value Integer to validate
     *
     * @return bool Parameter is valid integer or not
     */
    public static function isInt($value)
    {
        return (string) (int) $value === (string) $value || $value === false;
    }

    /**
     * Check for timestamp validity.
     * 
     * @param string $timestamp Timestamp to validate.
     *
     * @return bool Parameter is valid timestamp or not
     */
    public static function isTimeStamp($timestamp)
    {
        try {
            return ((string) (int) $timestamp === $timestamp)
                && ($timestamp <= PHP_INT_MAX)
                && ($timestamp >= ~PHP_INT_MAX);
        } catch (Exception $exception) {
            // writeToLog($exception);
            return false;
        }
    }

    /**
     * Check if $data is a string.
     *
     * @param string $data Data to validate
     *
     * @return bool Parameter is valid string or not
     */
    public static function isString($data)
    {
        return is_string($data);
    }
}
