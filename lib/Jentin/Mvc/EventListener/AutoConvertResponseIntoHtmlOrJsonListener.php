<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\EventListener;

use Jentin\Mvc\Event\ResponseFilterEvent;
use Jentin\Mvc\Response\Response;
use Jentin\Mvc\Response\JsonResponse;
use Jentin\Mvc\Response\ResponseInterface;

/**
 * HtmlJsonControllerResultListener.php
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class AutoConvertResponseIntoHtmlOrJsonListener
{

    /**
     * gets response
     *
     * @param  \Jentin\Mvc\Event\ResponseFilterEvent $event
     * @return \Jentin\Mvc\Response\ResponseInterface
     */
    public function getResponse(ResponseFilterEvent $event)
    {
        $responseContent = $event->getResponse();
        if ($responseContent instanceof ResponseInterface) {
            return $responseContent;
        }

        if (is_array($responseContent)) {
            $response = new JsonResponse();
            $response->setContent($responseContent);
        }
        else {
            $response = new Response();
            $response->setContentType('text/html; charset=utf-8');
            $response->setContent((string)$responseContent);
        }
        $event->setResponse($response);
        return $response;
    }

}
