<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Core;

/**
 * Util
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Util
{

    /**
     * gets camelcased string
     *
     * @param   string  $string
     * @return  string
     */
    public static function getCamelcased($string)
    {
        $string = strtolower($string);
        $string = preg_replace('/\W+?(\w)/e', 'ucfirst(\'\1\')', $string);
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
     * @param   bool    $camelcased
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
                $replaceWith[] = self::getCamelcased($values[$index]);
            }
        }
        return str_replace($searchFor, $replaceWith, $pattern);
    }

}