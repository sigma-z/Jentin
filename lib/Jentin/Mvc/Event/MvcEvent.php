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

/**
 * Static class, that defines Mvc events as class constants
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
abstract class MvcEvent extends \Symfony\Component\EventDispatcher\Event
{

    const ON_ROUTE                  = 'jentin.mvc.onRoute';
    const ON_CONTROLLER             = 'jentin.mvc.onController';
    const ON_CONTROLLER_RESULT      = 'jentin.mvc.onControllerResult';
    const ON_FILTER_RESPONSE        = 'jentin.mvc.onFilterResponse';
    const ON_CONTROLLER_DISPATCH    = 'jentin.mvc.onControllerDispatch';


    /**
     * @var \Jentin\Mvc\Response\ResponseInterface
     */
    protected $response;


    /**
     * sets response
     *
     * @param  \Jentin\Mvc\Response\ResponseInterface $response
     * @return \Jentin\Mvc\Event\MvcEvent
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }


    /**
     * gets response
     *
     * @return \Jentin\Mvc\Response\ResponseInterface
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
