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

/**
 * RouteEvent.php
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RouteEvent extends MvcEvent
{

    /**
     * @var RequestInterface
     */
    protected $request;


    /**
     * constructor
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }


    /**
     * gets request
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

}
