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
 * Router
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Router implements RouterInterface
{

    /**
     * @var array
     */
    protected $routes = array();

    protected $routedRoute;


    /**
     * gets route by name
     *
     * @param   string  $name
     * @return  array
     */
    public function getRoute($name)
    {
        if (!empty($this->routes[$name])) {
            return $this->routes[$name];
        }
        return null;
    }


    /**
     * sets route
     *
     * @param string $name
     * @param \Jentin\Mvc\Route\RouteInterface $route
     */
    public function setRoute($name, RouteInterface $route)
    {
        $this->routes[$name] = $route;
    }


    /**
     * gets routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }


    /**
     * sets routes
     *
     * @param array $routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }


    /**
     * parses routes
     *
     * @param   \Jentin\Mvc\Request\RequestInterface    $request
     * @param   array                                   $defaultParams optional
     *
     * @return  boolean
     */
    public function route(RequestInterface $request, array $defaultParams = array())
    {
        foreach ($this->routes as $name => $route) {
            if ($name === 'default') {
                continue;
            }
            if ($route->parse($request, $defaultParams)) {
                $this->routedRoute = $route;
                return true;
            }
        }

        $defaultRoute = $this->getRoute('default');
        if ($defaultRoute && $defaultRoute->parse($request, $defaultParams)) {
            $this->routedRoute = $defaultRoute;
            return true;
        }

        return false;
    }


    /**
     * gets url of route
     *
     * @param   string  $routeName
     * @param   array   $params
     * @param   string  $query
     * @param   string  $asterisk
     * @return  string
     * @throws  \DomainException
     */
    public function getUrl($routeName = '', array $params = array(), $query = '', $asterisk = '')
    {
        if (empty($routeName) && is_null($this->routedRoute)) {
            throw new \DomainException('No route has been routed, yet!');
        }
        $route = $this->getRoute($routeName);
        if (is_null($route)) {
            throw new \DomainException("Route $routeName is not defined!");
        }

        return $route->getUrl($params, $query, $asterisk);
    }

}