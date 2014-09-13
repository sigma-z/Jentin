<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Route;

use Jentin\Mvc\Request\RequestInterface;
use Jentin\Mvc\Response\ResponseInterface;

/**
 * RouteInterface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface RouteInterface
{

    /**
     * gets route url
     *
     * @return string
     */
    public function getPattern();

    /**
     * gets route url
     *
     * @param  array  $params
     * @param  string $query
     * @param  string $asterisk
     * @return string
     */
    public function getUrl(array $params = array(), $query = '', $asterisk = '');

    /**
     * gets route params
     *
     * @return array
     */
    public function getRouteParams();

    /**
     * sets route params
     *
     * @param array $routeParams
     */
    public function setRouteParams(array $routeParams);


    /**
     * parses the route
     *
     * @param  RequestInterface $request
     * @return boolean
     */
    public function parse(RequestInterface $request);


    /**
     * @param callable $callback
     */
    public function setCallback($callback);


    /**
     * @return bool
     */
    public function hasCallback();


    /**
     * @param  RequestInterface  $request
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public function callback(RequestInterface $request, ResponseInterface $response = null);

}
