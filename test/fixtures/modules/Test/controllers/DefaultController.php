<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TestModule;

use Jentin\Mvc\Controller\Controller;
use Jentin\Mvc\Response\RedirectResponse;

/**
 * DefaultController.php
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class DefaultController extends Controller
{

    public function homeAction()
    {
        return $this->plugin('view')->render();
    }


    public function noReturnResponseAction()
    {
        $content = $this->plugin('view')->render(array(), 'home');
        $this->response->setContent($content);
    }


    public function redirectAction()
    {
        return new RedirectResponse('http://example.com/');
    }


    public function testLayoutAction()
    {
        return $this->plugin('view')->render();
    }

}
