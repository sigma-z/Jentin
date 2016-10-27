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
 * RedirectResponse
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class RedirectResponse extends Response
{

    /**
     * constructor
     *
     * @param string $url
     */
    public function __construct($url = '')
    {
        $this->setStatus('301', 'Moved Permanently');
        if (!empty($url)) {
            $this->setRedirectUrl($url);
        }
    }


    /**
     * sets redirect url
     *
     * @param string $url
     * @return $this
     */
    public function setRedirectUrl($url)
    {
        return $this->setHeader('Location', $url);
    }


    /**
     * gets redirect url
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getHeader('Location');
    }


    /**
     * sends redirect response - overwrite parent::sendResponse()
     */
    public function sendResponse()
    {
        if (!$this->getRedirectUrl()) {
            throw new \InvalidArgumentException('Redirect url must be set!');
        }

        parent::sendResponse();
    }

}
