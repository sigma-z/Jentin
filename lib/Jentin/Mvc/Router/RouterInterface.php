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
     * @return RouteInterface
     */
    public function getRoute($name);


    /**
     * sets route
     *
     * @param string $name
     * @param RouteInterface $route
     */
    public function setRoute($name, RouteInterface $route);


    /**
     * @return RouteInterface[]
     */
    public function getRoutes();


    /**
     * @param RouteInterface[] $routes
     */
    public function setRoutes(array $routes);


    /**
     * routes the request
     *
     * @param   RequestInterface $request
     * @param   array                                   $defaultParams  default: array()
     * @return  RouteInterface
     */
    public function route(RequestInterface $request, array $defaultParams = array());

    /**
     * gets url
     *
     * @param  string  $routeName  default: ''
     * @param  array   $params     default: array()
     * @param  string  $query      default: ''
     * @param  string  $asterisk   default: ''
     * @return string
     */
    public function getUrl($routeName = '', array $params = array(), $query = '', $asterisk = '');

}
