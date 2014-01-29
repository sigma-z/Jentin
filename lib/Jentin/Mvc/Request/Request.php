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
 * Request
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Request implements RequestInterface
{
    /**
     * files
     * @var array
     */
    protected $files        = array();

    /**
     * server
     * @var array
     */
    protected $server       = array(
        'REQUEST_URI'   => '',
        'SCRIPT_NAME'   => '',
        'HTTP_HOST'     => 'localhost',
        'SERVER_NAME'   => '',
        'HTTPS'         => ''
    );

    /**
     * cookie
     * @var array
     */
    protected $cookie       = array();

    /**
     * module name
     * @var string
     */
    protected $moduleName   = 'default';

    /**
     * controller name
     * @var string
     */
    protected $controllerName = 'index';

    /**
     * action name
     * @var string
     */
    protected $actionName   = 'index';

    /**
     * params
     * @var array
     */
    protected $params       = array();

    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $scheme;


    /**
     * constructor
     *
     * @param array $params
     * @param array $server
     * @param array $cookie
     * @param array $files
     */
    public function __construct(array $params = null, array $server = null, array $cookie = null, array $files = null)
    {
        $this->params   = is_array($params)  ? $params   : $_REQUEST;
        $server         = is_array($server)  ? $server   : $_SERVER;
        $this->server = array_merge($this->server, $server);
        $this->cookie   = is_array($cookie)  ? $cookie   : $_COOKIE;
        $this->files    = is_array($files)   ? $files    : $_FILES;
    }


    /**
     * sets param by name
     *
     * @param   string  $key
     * @param   mixed   $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
    }


    /**
     * gets param by name
     *
     * @param   string  $name
     * @param   mixed   $default
     * @return  mixed
     */
    public function getParam($name, $default = null)
    {
        if (isset($this->params[$name])) {
            if (is_array($this->params[$name])) {
                return $this->params[$name];
            }
            else if ($this->params[$name] != '') {
                return trim($this->params[$name]);
            }
        }

        return $default;
    }


    /**
     * checks, if parameter is set
     *
     * @param   string  $name
     * @return  boolean
     */
    public function hasParam($name)
    {
        return array_key_exists($name, $this->params);
    }


    /**
     * gets raw data of param by name
     *
     * @param   string  $name
     * @param   mixed   $default
     * @return  mixed
     */
    public function getRawParam($name, $default = null)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        return $default;
    }


    /**
     * gets params
     *
     * @return array
     */
    public function getParams()
    {
    	return $this->params;
    }


    /**
     * merges existing params with given params, given params will overwrite existing params
     *
     * @param array $params
     */
    public function mergeParams(array $params)
    {
        $this->params = array_merge($this->params, $params);
    }


    /**
     * sets params
     *
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }


    /**
     * gets module name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }


    /**
     * sets module name
     *
     * @param string $moduleName
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }


    /**
     * sets controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }


    /**
     * sets controller name
     *
     * @param string $controllerName
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
    }


    /**
     * gets action name
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }


    /**
     * sets action name
     *
     * @param string $actionName
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
    }


    /**
     * sets request uri
     *
     * @param string $requestUri
     */
    public function setRequestUri($requestUri)
    {
        $this->server['REQUEST_URI'] = $requestUri;
    }


    /**
     * gets request uri
     *
     * @return string
     */
    public function getRequestUri()
    {
        return isset($this->server['REQUEST_URI'])
                ? $this->server['REQUEST_URI']
                : null;
    }


    /**
     * sets base path
     *
     * @param string $basePath
     */
    public function setBasePath($basePath = null)
    {
        if ($basePath === null) {
            $basePath = dirname($this->server['SCRIPT_NAME']);
        }
        $this->basePath = $basePath;
    }


    /**
     * gets base path
     *
     * @return string
     */
    public function getBasePath()
    {
        if ($this->basePath === null) {
            $this->setBasePath();
        }
        return $this->basePath;
    }


    /**
     * sets base url
     *
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl = null)
    {
        if ($baseUrl === null) {
            $basePath = $this->getBasePath();
            $baseUrl = $this->getRequestUri();

            if ($basePath && substr($baseUrl, 0, strlen($basePath)) == $basePath) {
                $baseUrl = substr($baseUrl, 0, strlen($basePath) + 1);
            }
            else {
                if (($pos = strpos('?', $baseUrl))) {
                    $baseUrl = substr($baseUrl, 0, $pos);
                }
                if ($baseUrl[strlen($baseUrl) - 1] != '/') {
                    $baseUrl = dirname($baseUrl) . '/';
                }
            }
        }
        $this->baseUrl = $baseUrl;
    }


    /**
     * gets base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->baseUrl === null) {
            $this->setBaseUrl();
        }
        return $this->baseUrl;
    }


    /**
     * sets host
     *
     * @param string $host
     */
    public function setHost($host = null)
    {
        if ($host === null) {
            if (isset($this->server['HTTP_HOST'])) {
                $host = $this->server['HTTP_HOST'];
            }
            else if (isset($this->server['SERVER_NAME'])) {
                $host = $this->server['SERVER_NAME'];
            }
        }

        $this->host = $host;
    }


    /**
     * gets host
     *
     * @return string
     */
    public function getHost()
    {
        if ($this->host === null) {
            $this->setHost();
        }
        return $this->host;
    }


    /**
     * sets scheme
     *
     * @param string $scheme
     */
    public function setScheme($scheme = null)
    {
        if ($scheme === null) {
            $scheme = isset($this->server['HTTPS']) && $this->server['HTTPS'] == 'on'
                    ? 'https'
                    : 'http';
        }
        $this->scheme = $scheme;
    }


    /**
     * gets scheme
     *
     * @return string
     */
    public function getScheme()
    {
        if ($this->scheme === null) {
            $this->setScheme();
        }
        return $this->scheme;
    }


    /**
     * gets header by name
     *
     * @param   string  $headerName
     * @return  string
     */
    public function getHeader($headerName)
    {
        // Try to get header information from the $_SERVER array
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $headerName));

        if (!empty($this->server[$key])) {
            return $this->server[$key];
        }

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();

            if (!empty($headers[$headerName])) {
                return $headers[$headerName];
            }
        }

        return null;
    }


    /**
     * checks, if request was a xml http request, or not
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        return $this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest';
    }


    /**
     * gets url query
     *
     * @return string
     */
    public function getQuery()
    {
        $requestUri = $this->getRequestUri();
        $urlParts = parse_url($requestUri);
        if (isset($urlParts['query'])) {
            return $urlParts['query'];
        }
        return '';
    }


    /**
     * gets url fragment
     *
     * @return string
     */
    public function getFragment()
    {
        $requestUri = $this->getRequestUri();
        $urlParts = parse_url($requestUri);
        if (isset($urlParts['fragment'])) {
            return $urlParts['fragment'];
        }
        return '';
    }

}
