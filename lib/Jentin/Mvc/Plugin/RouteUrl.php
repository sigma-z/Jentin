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

    protected $router;
    protected $request;


    public function __construct(RouterInterface $router, RequestInterface $request)
    {
        $this->router = $router;
        $this->request = $request;
    }


    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }


    public function getRouter()
    {
        return $this->router;
    }


    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }


    public function getRequest()
    {
        return $this->request;
    }


    public function url($name = null, array $params = array())
    {
        $this->router->getUrl();
    }

}