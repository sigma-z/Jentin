<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Controller;

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
     *
     * @param  mixed $response
     * @return mixed
     */
    public function postDispatch($response);


    /**
     * Dispatches action
     */
    public function dispatch();


    /**
     * @return \Jentin\Mvc\Request\RequestInterface
     */
    public function getRequest();

}
