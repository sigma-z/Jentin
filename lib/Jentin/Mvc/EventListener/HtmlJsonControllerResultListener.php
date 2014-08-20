<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\EventListener;

use Jentin\Mvc\Event\ControllerResultEvent;
use Jentin\Mvc\Response\Response;
use Jentin\Mvc\Response\JsonResponse;

/**
 * HtmlJsonControllerResultListener.php
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class HtmlJsonControllerResultListener
{

    /**
     * gets response
     *
     * @param  \Jentin\Mvc\Event\ControllerResultEvent $event
     * @return \Jentin\Mvc\Response\ResponseInterface
     */
    public function getResponse(ControllerResultEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        if (is_array($controllerResult)) {
            $response = new JsonResponse();
            $response->setContent($controllerResult);
        }
        else {
            $response = new Response();
            $response->setContentType('text/html; charset=utf-8');
            $response->setContent((string)$controllerResult);
        }
        $event->setResponse($response);
        return $response;
    }

}
