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

    /**
     * @var \Jentin\Mvc\Router\RouterInterface
     */
    protected $router;
    /**
     * @var \Jentin\Mvc\Request\RequestInterface
     */
    protected $request;


    /**
     * constructor
     *
     * @param \Jentin\Mvc\Router\RouterInterface $router
     * @param \Jentin\Mvc\Request\RequestInterface $request
     */
    public function __construct(RouterInterface $router, RequestInterface $request)
    {
        $this->router = $router;
        $this->request = $request;
    }


    /**
     * sets router
     *
     * @param \Jentin\Mvc\Router\RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }


    /**
     * gets router
     *
     * @return \Jentin\Mvc\Router\RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }


    /**
     * sets request
     *
     * @param \Jentin\Mvc\Request\RequestInterface $request
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }


    /**
     * gets router
     *
     * @return \Jentin\Mvc\Request\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

}