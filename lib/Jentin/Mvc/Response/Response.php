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

    /**
     * @var string
     */
    protected $content = '';
    /**
     * @var integer
     */
    protected $statusCode = 200;
    /**
     * @var string
     */
    protected $statusMessage = 'OK';
    /**
     * @var array
     */
    protected $headers = array();


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
     * @param   string  $name
     * @param   string  $value
     */
    public function setHeader($name, $value = '')
    {
        if ($value === null) {
            unset($this->headers[$name]);
        }
        else {
            $this->headers[$name] = $value;
        }
    }


    /**
     * gets header
     *
     * @param   string  $name
     * @return  string
     */
    public function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }
        return null;
    }


    /**
     * Checks, if headers are set for this response
     *
     * @return bool
     */
    public function hasHeaders()
    {
        return !empty($this->headers);
    }


    /**
     * sets content type
     *
     * @param  string   $contentType
     */
    public function setContentType($contentType)
    {
        $this->setHeader('Content-Type', $contentType);
    }


    /**
     * sends response headers
     *
     * @param  bool $throwExceptionOnHeadersSent
     * @throws ResponseException
     */
    public function sendHeaders($throwExceptionOnHeadersSent = true)
    {
        // throws exception when headers already sent
        $this->canSendHeaders($throwExceptionOnHeadersSent);

        $this->sendHeader("HTTP/1.1 $this->statusCode $this->statusMessage");
        $contentType = $this->getHeader('Content-Type');
        if (!$contentType) {
            $contentType = 'text/html; charset=utf-8';
        }
        $this->sendHeader("Content-Type: $contentType");

        foreach ($this->headers as $name => $value) {
            if ($name == 'Content-Type') {
                continue;
            }
            $header = $value === true ? $name : "$name: $value";
            $this->sendHeader($header);
        }
    }


    /**
     * appends content
     *
     * @param   string  $content
     */
    public function appendContent($content)
    {
        $this->content .= $content;
    }


    /**
     * gets content
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }


    /**
     * sets content
     *
     * @param   string  $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }


    /**
     * sets http response status
     *
     * @param  integer  $code
     * @param  string   $message
     */
    public function setStatus($code, $message = '')
    {
        $this->statusCode = $code;
        $this->statusMessage = $message;
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
     * sends response to browser
     */
    public function sendResponse()
    {
        $this->sendHeaders();
        echo $this->content;
    }


    public function flushResponse()
    {
        $this->sendResponse();
        $this->headers = array();
        $this->content = '';
    }


    /**
     * @param  bool $throwExceptionOnHeadersSent
     * @return bool
     * @throws ResponseException
     */
    public function canSendHeaders($throwExceptionOnHeadersSent = true)
    {
        $headersSent = headers_sent($file, $line);
        if ($headersSent && $throwExceptionOnHeadersSent) {
            throw new ResponseException("Headers has been already sent! (file: $file in line $file)");
        }
        return $headersSent === false;
    }


    /**
     * @param string $header
     * @param bool   $overwrite
     */
    protected function sendHeader($header, $overwrite = true)
    {
        header($header, $overwrite);
    }

}
