<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Controller;

use Jentin\Mvc\Plugin\PluginBrokerInterface;

/**
 * ControllerAware
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface ControllerAware
{

    /**
     * sets controller
     *
     * @param  ControllerInterface $controller
     * @return ControllerAware
     */
    public function setController(ControllerInterface $controller);

    /**
     * gets controller
     *
     * @return ControllerInterface
     */
    public function getController();

    /**
     * @return PluginBrokerInterface
     */
    public function getPluginBroker();
}
