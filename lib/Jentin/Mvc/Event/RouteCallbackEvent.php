<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Event;

use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Route\RouteInterface;

/**
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RouteCallbackEvent extends MvcEvent
{

    /** @var RequestInterface */
    protected $request;

    /** @var RouteInterface */
    protected $route;


    /**
     * @param RequestInterface $request
     * @param RouteInterface   $route
     */
    public function __construct(RequestInterface $request, RouteInterface $route)
    {
        $this->request = $request;
        $this->route = $route;
    }


    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * @return RouteInterface
     */
    public function getRoute()
    {
        return $this->route;
    }

}
