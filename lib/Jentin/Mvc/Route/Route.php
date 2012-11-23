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

/**
 * routes for routing a request
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */

class Route implements RouteInterface
{

    /**
     * constant placeholder pattern
     */
    const PLACEHOLDER_PATTERN = '%([\w:\.\-]+?)%';

    /**
     * @var string
     */
    protected $pattern = '';
    /**
     * @var array
     */
    protected $routeParams = array();


    /**
     * constructor
     *
     * @param   string  $pattern
     * @param   array   $routeParams
     */
    public function __construct($pattern, array $routeParams = array())
    {
        $this->pattern     = $pattern;
        $this->routeParams = $routeParams;
    }

    /**
     * gets route url
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }


    /**
     * sets route params
     *
     * @param  array $routeParams
     */
    public function setRouteParams(array $routeParams)
    {
        $this->routeParams = $routeParams;
    }


    /**
     * gets route params
     *
     * @return  array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }


    /**
     * parses route for given url
     *
     * @param   \Jentin\Mvc\Request\RequestInterface $request
     * @return  boolean
     *
     * @throws  RouteException if base url does not match the request uri
     */
    public function parse(RequestInterface $request)
    {
        $requestUrl = $this->getRequestUriWithoutQueryString($request);

        $baseUrl    = $request->getBaseUrl();
        if ($baseUrl != substr($requestUrl, 0, strlen($baseUrl))) {
            throw new RouteException(
                "Request url does not match base url! Request url: '$requestUrl' Base url: $baseUrl"
            );
        }

        $url = substr($requestUrl, strlen($baseUrl) - 1);
        $params = $this->parseRouteParams($this->pattern, $url);

        if ($params === false) {
            return false;
        }

        $params = array_merge($request->getParams(), $params, $this->routeParams);

        if (isset($params['module'])) {
            $request->setModuleName($params['module']);
            unset($params['module']);
        }
        if (isset($params['controller'])) {
            $request->setControllerName($params['controller']);
            unset($params['controller']);
        }
        if (isset($params['action'])) {
            $request->setActionName($params['action']);
            unset($params['action']);
        }

        $request->setParams($params);

        return true;
    }


    protected function getRequestUriWithoutQueryString(RequestInterface $request)
    {
        $requestUri = $request->getRequestUri();
        if (false !== ($pos = strpos($requestUri, '?'))) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        return $requestUri;
    }


    /**
     * parses route params
     *
     * @param   string  $routePattern
     * @param   string  $url
     * @return  array|bool
     */
    protected function parseRouteParams($routePattern, $url)
    {
        $params = array();
        $routeQuoted = preg_quote($routePattern, '|');
        $search = array('\\(', '\\)');
        $replace = array('(', ')?');
        $routeQuoted = str_replace($search, $replace, $routeQuoted);
        $replace = '(?<P\\1>.+?)';
        $routePattern = preg_replace('|' . self::PLACEHOLDER_PATTERN . '|', $replace, $routeQuoted);
        if (preg_match('|^' . $routePattern . '$|', $url, $matches)) {
            foreach ($matches as $name => $value) {
                if ($name[0] == 'P') {
                    $params[substr($name, 1)] = rtrim($value, '/');
                }
            }
            return $params;
        }
        return false;
    }


    /**
     * gets url for route by given params
     *
     * @param array  $params
     * @param string $query
     * @param string $asterisk
     * @return string
     * @throws \DomainException if route misses params
     */
    public function getUrl(array $params = array(), $query = '', $asterisk = '')
    {
        $url = '';
        // parse for placeholders in pattern
        $placeHolders = array();
        if (preg_match_all('|' . self::PLACEHOLDER_PATTERN . '|', $this->pattern, $matches)) {
            $placeHolders = $matches[1];
        }

        if ($placeHolders) {
            $url = \Jentin\Core\Util::parsePattern($this->pattern, $params, '%', false);
            // check if optional placeholders are not replaced through parse
            $placeHoldersSubPattern = implode('|', $placeHolders);
            $regExpr = '\([^\(]?%(' . $placeHoldersSubPattern . ')%.*?\)';
            if (preg_match(':' . $regExpr . ':', $url, $matches, PREG_OFFSET_CAPTURE)) {
                $url = substr($url, 0, $matches[0][1]);
            }
            // check if mandatory placeholders are not replaced through parse
            if (preg_match_all(':%(' . $placeHoldersSubPattern . ')%:', $url, $matches)) {
                throw new \DomainException(
                    "Missing params for getting url of route '$this->pattern': " . implode(', ', $matches[1])
                );
            }
            // remove brackets of optional parts
            $url = str_replace(array('(', ')'), '', $url);
        }

        // add query string
        if ($query) {
            $url .= '?' . $query;
        }
        // add asterisk
        if ($asterisk) {
            $url .= '#' . $asterisk;
        }
        return $url;
    }

}