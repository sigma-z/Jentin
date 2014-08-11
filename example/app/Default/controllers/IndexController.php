<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DefaultModule;

/**
 * DefaultController
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class IndexController extends \Jentin\Mvc\Controller\Controller
{

    public function indexAction()
    {
        $view = $this->plugin('view');
        $view->framework = 'Jentin MVC framework';
        $view->readme = file_get_contents(__DIR__ . '/../../../../README.md');
        return $view->render();
    }

}
