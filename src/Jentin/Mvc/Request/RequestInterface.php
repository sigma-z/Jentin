<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Request;

/**
 * RequestInterface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface RequestInterface
{

    /**
     * sets param by name
     *
     * @param   string  $key
     * @param   mixed   $value
     */
    public function setParam($key, $value);

    /**
     * gets param by name
     *
     * @param   string  $name
     * @param   mixed   $default
     * @return  mixed
     */
    public function getParam($name, $default = null);

    /**
     * sets params
     *
     * @param array $params
     */
    public function setParams(array $params);

    /**
     * gets params
     *
     * @return array
     */
    public function getParams();

    /**
     * Returns true, if request param is a post parameter
     *
     * @param  string $name
     * @return bool
     */
    public function isPost($name);

    /**
     * Returns true, if request param is a get parameter
     *
     * @param  string $name
     * @return bool
     */
    public function isGet($name);

    /**
     * gets module name
     *
     * @return string
     */
    public function getModuleName();

    /**
     * sets module name
     *
     * @param string $moduleName
     */
    public function setModuleName($moduleName);

    /**
     * sets controller name
     *
     * @return string
     */
    public function getControllerName();

    /**
     * sets controller name
     *
     * @param string $controllerName
     */
    public function setControllerName($controllerName);

    /**
     * gets action name
     *
     * @return string
     */
    public function getActionName();

    /**
     * sets action name
     *
     * @param string $actionName
     */
    public function setActionName($actionName);

    /**
     * gets request uri
     *
     * @return string
     */
    public function getRequestUri();

    /**
     * sets request uri
     *
     * @param string $requestUri
     */
    public function setRequestUri($requestUri);

    /**
     * sets base url
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl = null);

    /**
     * gets base url
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * sets base path
     *
     * @param string $basePath
     */
    public function setBasePath($basePath = null);

    /**
     * gets base path
     *
     * @return string
     */
    public function getBasePath();

    /**
     * gets server var
     *
     * @param  string|null $name
     * @param  mixed|null  $default
     * @return mixed
     */
    public function getServer($name = null, $default = null);

    /**
     * gets cookie by name
     *
     * @param  string $name
     * @param  mixed  $default
     * @return mixed
     */
    public function getCookie($name, $default = null);

    /**
     * gets url query
     *
     * @return string
     */
    public function getQuery();


    /**
     * gets url fragment
     *
     * @return string
     */
    public function getFragment();


    /**
     * Sets the request as dispatched, set by the controller
     *
     * @param  bool $isDispatched
     */
    public function setDispatched($isDispatched = true);


    /**
     * If the request has not been dispatched, yet, it returns false
     *
     * @return bool
     */
    public function isDispatched();

}
