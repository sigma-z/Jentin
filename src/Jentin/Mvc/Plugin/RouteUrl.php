<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Plugin;

use Jentin\Mvc\Router\RouterInterface;
use Jentin\Mvc\Request\RequestInterface;

/**
 * RouteUrl
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RouteUrl
{

    /** @var RouterInterface */
    protected $router;

    /** @var RequestInterface */
    protected $request;


    /**
     * constructor
     *
     * @param RouterInterface $router
     * @param RequestInterface $request
     */
    public function __construct(RouterInterface $router, RequestInterface $request)
    {
        $this->router = $router;
        $this->request = $request;
    }


    /**
     * sets router
     *
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }


    /**
     * gets router
     *
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }


    /**
     * sets request
     *
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }


    /**
     * gets router
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * @param  string $routeName
     * @param  array  $params
     * @param  string $query
     * @param  string $asterisk
     * @return string
     */
    public function url($routeName, array $params = array(), $query = '', $asterisk = '')
    {
        return $this->router->getRoute($routeName)
            ->getUrl($params, $query, $asterisk);
    }

}
