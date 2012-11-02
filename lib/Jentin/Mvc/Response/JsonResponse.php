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
 * JsonResponse
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class JsonResponse extends Response
{

    /**
     * constructor
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        parent::__construct($content);
        $this->setContentType('text/x-json');
    }


    /**
     * sends response - overwrites parent::sendResponse()
     */
    public function sendResponse()
    {
        $this->sendHeaders();
        echo json_encode($this->content);
    }

}