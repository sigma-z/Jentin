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
 * ResponseInterface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface ResponseInterface
{

    /**
     * sets header
     *
     * @param   string  $name
     * @param   string  $value
     */
    public function setHeader($name, $value = '');

    /**
     * gets header
     *
     * @param   string  $name
     * @return  string
     */
    public function getHeader($name);

    /**
     * Checks, if headers are set for this response
     *
     * @return bool
     */
    public function hasHeaders();

    /**
     * append content
     *
     * @param string $content
     */
    public function appendContent($content);

    /**
     * gets content
     *
     * @return string
     */
    public function getContent();

    /**
     * sets content
     *
     * @param string $content
     */
    public function setContent($content);

    /**
     * send response headers
     */
    public function sendHeaders();

    /**
     * sends response
     */
    public function sendResponse();

}
