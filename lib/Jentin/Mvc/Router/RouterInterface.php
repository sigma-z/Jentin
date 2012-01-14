<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Router;

use Jentin\Mvc\Route\RouteInterface;
use Jentin\Mvc\Request\RequestInterface;

/**
 * RouterInterface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface RouterInterface
{

    /**
     * gets route by name
     *
     * @param  string $name
     * @return Route
     */
    public function getRoute($name);


    /**
     * sets route
     *
     * @param string $name
     * @param \Jentin\Mvc\Route\RouteInterface $route
     */
    public function setRoute($name, RouteInterface $route);


    /**
     * routes the request
     *
     * @param   \Jentin\Mvc\Request\RequestInterface    $request
     * @param   array                                   $defaultParams  optional
     * @return  boolean
     */
    public function route(RequestInterface $request, array $defaultParams = array());

    /**
     * gets url
     *
     * @param string $routeName
     * @param array  $params
     * @param string $query
     * @param string $asterisk
     */
    public function getUrl($routeName = '', array $params = array(), $query = '', $asterisk = '');

}