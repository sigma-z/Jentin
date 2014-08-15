<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Util;

/**
 * Util
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Util
{

    /**
     * gets camel-cased string
     *
     * @param   string  $string
     * @return  string
     */
    public static function getCamelCased($string)
    {
        $string = strtolower($string);
        $string = preg_replace_callback(
            '/\W+?(\w)/',
            function ($matches) {
                return ucfirst($matches[1]);
            },
            $string
        );
        $string = ucfirst($string);
        return $string;
    }


    /**
     * parses pattern
     *
     * @static
     * @param   string  $pattern
     * @param   array   $params
     * @param   string  $delimiter
     * @param   bool    $camelCased
     * @return  string
     */
    public static function parsePattern($pattern, array $params, $delimiter = '%', $camelCased = true)
    {
        $keys = array_keys($params);
        $values = array_values($params);
        $replaceWith = array();
        $searchFor = array();
        foreach ($keys as $index => $key) {
            $searchFor[] = $delimiter . $key . $delimiter;
            $replaceWith[] = $values[$index];

            if ($camelCased) {
                $searchFor[] = $delimiter . ucfirst($key) . $delimiter;
                $replaceWith[] = self::getCamelCased($values[$index]);
            }
        }
        return str_replace($searchFor, $replaceWith, $pattern);
    }

}
