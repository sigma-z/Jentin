<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Controller;

use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\ResponseInterface;

/**
 * ControllerInterface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface ControllerInterface
{

    /**
     * Should be executed before dispatching the controller action through HttpKernel::dispatch()
     */
    public function preDispatch();


    /**
     * Should be executed after dispatching the controller action through HttpKernel::dispatch()
     */
    public function postDispatch();


    /**
     * Dispatches action
     */
    public function dispatch();


    /**
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request);


    /**
     * @return RequestInterface
     */
    public function getRequest();


    /**
     * @param ResponseInterface $response
     */
    public function setResponse(ResponseInterface $response);


    /**
     * Returns the response, which can be a response object or a mixed type (eg. string for HTML or array for JSON)
     *
     * @return ResponseInterface|mixed
     */
    public function getResponse();

}
