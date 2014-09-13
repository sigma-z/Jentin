<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Mvc\Plugin;

/**
 * PluginAwareInterface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface Pluggable
{

    /**
     * sets plugin broker
     *
     * @param  PluginBrokerInterface $broker
     * @return Pluggable
     */
    public function setPluginBroker(PluginBrokerInterface $broker);

    /**
     * gets plugin broker
     *
     * @return \Jentin\Mvc\Plugin\PluginBrokerInterface
     */
    public function getPluginBroker();

    /**
     * gets (and loads) plugin by a given name
     *
     * @param   string  $name
     * @return  object|callable
     */
    public function plugin($name);

}
