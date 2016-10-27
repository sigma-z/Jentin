<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Response;

/**
 * Response
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class Response implements ResponseInterface
{

    /** @var string */
    protected $content = '';

    /** @var int */
    protected $statusCode = 200;

    /** @var string */
    protected $statusMessage = 'OK';

    /** @var array */
    protected $headers = array();

    /** @var ResponseCookie[] */
    protected $cookies = array();

    /** @var bool */
    protected $statusSent = false;


    /**
     * constructor
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = $content;
    }


    /**
     * sets header
     *
     * @param string $name
     * @param string $value
     * @param bool   $replace
     * @return $this
     */
    public function setHeader($name, $value = '', $replace = true)
    {
        $header = array(
            'value' => $value,
            'replace' => $replace,
            'sent' => false
        );
        if ($replace) {
            $this->headers[$name] = array($header);
        }
        else {
            $this->headers[$name][] = $header;
        }
        return $this;
    }


    /**
     * @param string $name
     * @return $this
     */
    public function unsetHeader($name)
    {
        unset($this->headers[$name]);
        return $this;
    }


    /**
     * gets header
     *
     * @param string $name
     * @param mixed  $default
     * @return string|array|null
     */
    public function getHeader($name, $default = null)
    {
        if (isset($this->headers[$name])) {
            $values = array();
            foreach ($this->headers[$name] as $headerEntry) {
                $values[] = $headerEntry['value'];
            }
            return isset($values[1]) ? $values : $values[0];
        }
        return $default;
    }


    /**
     * @return array
     */
    public function getHeaderSent()
    {
        $headersSent = array();
        foreach ($this->headers as $name => $headerEntries) {
            foreach ($headerEntries as $headerEntry) {
                if ($headerEntry['sent'] === true) {
                    $headersSent[] = $headerEntry['value'] ? $name . ': ' . $headerEntry['value'] : $name;
                }
            }
        }
        return $headersSent;
    }


    /**
     * sets content type
     *
     * @param string $contentType
     * @return $this
     */
    public function setContentType($contentType)
    {
        return $this->setHeader('Content-Type', $contentType);
    }


    /**
     * sends response headers
     *
     * @param bool $throwExceptionOnHeadersSent
     * @throws ResponseException
     */
    public function sendHeaders($throwExceptionOnHeadersSent = true)
    {
        if ($this->havingPendingHeaders()) {
            // throws exception when headers already sent
            $this->canSendHeaders($throwExceptionOnHeadersSent);
            $this->sendStatusHeader();
            $contentType = $this->getHeader('Content-Type');
            if (!$contentType) {
                $contentType = 'text/html; charset=utf-8';
                $this->setHeader('Content-Type', $contentType);
            }
            foreach ($this->headers as $name => $headerEntries) {
                foreach ($headerEntries as $index => $headerEntry) {
                    $this->sendHeader($name . ': ' . $headerEntry['value'], $headerEntry['replace']);
                    $this->headers[$name][$index]['sent'] = true;
                }
            }
            $this->sendCookies();
        }
    }


    /**
     * sets body/content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }


    /**
     * appends body/content
     *
     * @param string $content
     * @return $this
     */
    public function appendContent($content)
    {
        $this->content .= $content;
        return $this;
    }


    /**
     * gets body/content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * alias of setContent()
     *
     * @param string $content
     * @return $this
     */
    public function setBody($content)
    {
        return $this->setContent($content);
    }


    /**
     * alias of appendContent()
     *
     * @param string $content
     * @return $this
     */
    public function appendBody($content)
    {
        return $this->appendContent($content);
    }


    /**
     * alias of getContent()
     *
     * @return string
     */
    public function getBody()
    {
        return $this->getContent();
    }


    /**
     * sets http response status
     *
     * @param int     $code
     * @param string  $message
     * @return $this
     */
    public function setStatus($code, $message = '')
    {
        $this->statusCode = $code;
        $this->statusMessage = $message;
        return $this;
    }


    /**
     * gets http response status code
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


    /**
     * gets http response status message
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }


    /**
     * @param ResponseCookie $cookie
     */
    public function setCookie(ResponseCookie $cookie)
    {
        $this->cookies[] = $cookie;
    }


    /**
     * sends response to browser
     */
    public function sendResponse()
    {
        $this->sendHeaders();
        $this->sendContent();
    }


    public function sendContent()
    {
        if ($this->content !== '') {
            echo $this->content;
        }
    }


    public function flushResponse()
    {
        $this->sendHeaders();
        $this->sendContent();
        $this->content = '';
    }


    /**
     * @param bool $throwExceptionOnHeadersSent
     * @return bool
     * @throws ResponseException
     */
    public function canSendHeaders($throwExceptionOnHeadersSent = true)
    {
        $headersSent = headers_sent($file, $line);
        if ($headersSent && $throwExceptionOnHeadersSent) {
            throw new ResponseException("Headers has been already sent! (file: $file in line $line)");
        }
        return $headersSent === false;
    }


    /**
     * @param string $header
     * @param bool   $replace
     */
    protected function sendHeader($header, $replace = true)
    {
        header($header, $replace);
    }


    protected function sendCookies()
    {
        foreach ($this->cookies as $cookie) {
            setcookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpire(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
        }
        $this->cookies = array();
    }


    protected function sendStatusHeader()
    {
        if (!$this->statusSent) {
            $this->sendHeader("HTTP/1.1 $this->statusCode $this->statusMessage");
            $this->statusSent = true;
        }
    }


    /**
     * @return bool
     */
    protected function havingPendingHeaders()
    {
        if (!$this->statusSent) {
            return true;
        }

        foreach ($this->headers as $name => $headerEntries) {
            foreach ($headerEntries as $headerEntry) {
                if ($headerEntry['sent'] === false) {
                    return true;
                }
            }
        }
        return false;
    }

}
