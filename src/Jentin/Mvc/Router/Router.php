<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Router;

use Jentin\Mvc\Route\Route;
use Jentin\Mvc\Route\RouteInterface;
use Jentin\Mvc\Request\RequestInterface;


/**
 * Router
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Router implements RouterInterface
{

    const DEFAULT_ROUTE_NAME = 'default';

    const DEFAULT_ROUTE_PATTERN = '(/%module%)(/%controller%)(/%action%)(/*)';


    /** @var RouteInterface[] */
    protected $routes = array();

    /** @var RouteInterface */
    protected $routedRoute;


    /**
     * gets route by name
     *
     * @param   string  $name
     * @return  RouteInterface
     */
    public function getRoute($name)
    {
        if (!empty($this->routes[$name])) {
            return $this->routes[$name];
        }
        if ($name === self::DEFAULT_ROUTE_NAME) {
            return new Route(self::DEFAULT_ROUTE_PATTERN);
        }
        return false;
    }


    /**
     * adds route
     *
     * @param  string                $name
     * @param  RouteInterface|string $route
     * @return RouteInterface
     */
    public function addRoute($name, $route)
    {
        if (is_string($route)) {
            $route = new Route($route);
        }
        $this->routes[$name] = $route;
        return $route;
    }


    /**
     * adds route
     *
     * @param  string $name
     * @param  RouteInterface|string $route
     * @return RouteInterface
     */
    public function setRoute($name, $route)
    {
        return $this->addRoute($name, $route);
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
     * @param RouteInterface[] $routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }


    /**
     * parses routes
     *
     * @param  RequestInterface $request
     * @param  array            $defaultParams default: array()
     *
     * @return RouteInterface
     */
    public function route(RequestInterface $request, array $defaultParams = array())
    {
        foreach ($this->routes as $name => $route) {
            if ($name === self::DEFAULT_ROUTE_NAME) {
                continue;
            }
            if ($route->parse($request, $defaultParams)) {
                return $route;
            }
        }
        $defaultRoute = $this->getRoute(self::DEFAULT_ROUTE_NAME);
        if ($defaultRoute && $defaultRoute->parse($request, $defaultParams)) {
            return $defaultRoute;
        }
        return null;
    }


    /**
     * gets url of route
     *
     * @param  string  $routeName  default: ''
     * @param  array   $params     default: array()
     * @param  string  $query      default: ''
     * @param  string  $asterisk   default: ''
     * @return string
     * @throws \DomainException
     */
    public function getUrl($routeName = '', array $params = array(), $query = '', $asterisk = '')
    {
        if (empty($routeName)) {
            $routeName = self::DEFAULT_ROUTE_NAME;
        }
        $route = $this->getRoute($routeName);
        if (!$route) {
            throw new \DomainException("Route $routeName is not defined!");
        }
        return $route->getUrl($params, $query, $asterisk);
    }

}
