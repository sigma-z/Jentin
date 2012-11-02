<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Response;

use Jentin\Mvc\Response\ResponseInterface;

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
     */
    public function sendHeaders()
    {
        if (headers_sent($file, $line)) {
            throw new ResponseException(
                    "Headers has been already sent! (file: $file in line $file)"
            );
        }

        header('HTTP/1.0 ' . $this->statusCode . ' ' . $this->statusMessage);
        $contentType = $this->getHeader('Content-Type');
        if (!$contentType) {
            $contentType = 'text/html; charset=utf-8';
        }
        header('Content-Type: ' . $contentType, true);

        foreach ($this->headers as $name => $header) {
            if ($name == 'Content-Type') {
                continue;
            }
            if ($header === true) {
                header($name, true);
            }
            else {
                header($name . ': ' . $header, true);
            }
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

}