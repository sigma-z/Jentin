<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Event;

use Jentin\Mvc\Response\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Static class, that defines Mvc events as class constants
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
abstract class MvcEvent extends Event
{

    const ON_ROUTE                  = 'jentin.mvc.onRoute';
    const ON_ROUTE_CALLBACK         = 'jentin.mvc.onRouteCallback';
    const ON_CONTROLLER             = 'jentin.mvc.onController';
    const ON_CONTROLLER_DISPATCH    = 'jentin.mvc.onControllerDispatch';
    const ON_FILTER_RESPONSE        = 'jentin.mvc.onFilterResponse';
    const ON_CONTROLLER_RESULT      = 'jentin.mvc.onControllerResult';


    /** @var ResponseInterface|mixed */
    protected $response;


    /**
     * sets response
     *
     * @param  ResponseInterface|mixed $response
     * @return MvcEvent
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }


    /**
     * gets response
     *
     * @return ResponseInterface|mixed
     */
    public function getResponse()
    {
        return $this->response;
    }


    /**
     * returns true, if response is set
     *
     * @return bool
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }

}
