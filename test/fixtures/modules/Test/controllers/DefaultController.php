<?php

namespace TestModule;

/**
 * TestController.php
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class DefaultController extends \Jentin\Mvc\Controller\Controller
{

    public function homeAction()
    {
        return $this->plugin('view')->render();
    }


    public function redirectAction()
    {
        return new \Jentin\Mvc\Response\RedirectResponse('http://example.com/');
    }


    public function testLayoutAction()
    {
        return $this->plugin('view')->render();
    }

}
