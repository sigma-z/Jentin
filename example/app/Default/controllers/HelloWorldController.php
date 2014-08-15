<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace DefaultModule;

use Jentin\Mvc\Controller\Controller;
use Jentin\Mvc\Response\JsonResponse;
use Jentin\Mvc\Response\Response;

/**
 * @author  Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class HelloWorldController extends Controller
{

    public function jsonAction()
    {
        return new JsonResponse(array('message' => 'hello world'));
    }

    public function simpleJsonAction()
    {
        return array('message' => 'hello world');
    }

    public function htmlAction()
    {
        return new Response('hello world');
    }

    public function simpleHtmlAction()
    {
        return 'hello world';
    }

}
