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
 * PluginBrokerInterface
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
interface PluginBrokerInterface
{

    /**
     * registers a plugin by name by giving an instance or fully qualified class name
     *
     * @param   string          $name
     * @param   string|object   $plugin
     * @return  PluginBrokerInterface
     */
    public function register($name, $plugin);

    /**
     * unregister plugin by name
     *
     * @param   string  $name
     * @return  boolean
     */
    public function unregister($name);

    /**
     * returns true, if plugin by name is registered
     *
     * @param   string $name
     * @return  boolean
     */
    public function isRegistered($name);

    /**
     * loads plugin by name
     *
     * @param   string  $name
     * @return  object
     */
    public function load($name);

}
