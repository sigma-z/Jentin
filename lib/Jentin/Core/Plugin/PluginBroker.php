<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jentin\Core\Plugin;

/**
 * PluginBroker
 * @author Steffen Zeidler <sigma_z@sigma-scripts.de>
 */
class PluginBroker implements PluginBrokerInterface
{

    /**
     * registered plugins
     * @var array
     */
    protected $plugins = array();


    /**
     * constructor
     *
     * @param array $plugins
     */
    public function __construct(array $plugins = array())
    {
        $this->registerPlugins($plugins);
    }


    /**
     * registers plugins
     *
     * @param   array   $plugins
     * @return  PluginBroker
     */
    public function registerPlugins(array $plugins)
    {
        $this->plugins = $plugins;
        return $this;
    }


    /**
     * registers a plugin by name by giving an instance or fully qualified class name
     *
     * @param   string          $name
     * @param   string|object   $plugin
     * @return  PluginBroker
     */
    public function register($name, $plugin)
    {
        $name = strtolower($name);
        $this->plugins[$name] = $plugin;
        return $this;
    }


    /**
     * unregister plugin by name
     *
     * @param   string  $name
     * @return  boolean
     */
    public function unregister($name)
    {
        if ($this->isRegistered($name)) {
            $name = strtolower($name);
            unset($this->plugins[$name]);
            return true;
        }
        return false;
    }


    /**
     * returns true, if plugin by name is registered
     *
     * @param   string $name
     * @return  boolean
     */
    public function isRegistered($name)
    {
        $name = strtolower($name);
        return isset($this->plugins[$name]);
    }


    /**
     * loads plugin by name
     *
     * @param   string  $name
     * @return  object
     */
    public function load($name)
    {
        $name = strtolower($name);
        if (!$this->isRegistered($name)) {
            throw new \DomainException("Plugin '$name' is not defined!");
        }

        // return plugin instance, if it has been initialized already
        if (is_object($this->plugins[$name])) {
            return $this->plugins[$name];
        }

        $instance = null;
        // create instance, if plugin has been defined as class name
        if (is_string($this->plugins[$name])) {
            $instance = new $this->plugins[$name];
        }
        // create instance, if plugin has been defined as array with keys: class name and args
        else if (is_array($this->plugins[$name])
                 && (isset($this->plugins[$name]['class']) || isset($this->plugins[$name][0])))
        {
            if (isset($this->plugins[$name]['class'])) {
                $class = $this->plugins[$name]['class'];
                $args = isset($this->plugins[$name]['args']) ? $this->plugins[$name]['args'] : array();
            }
            else {
                $class = $this->plugins[$name][0];
                $args = isset($this->plugins[$name][1]) ? $this->plugins[$name][1] : array();
            }

            $reflectionClass = new \ReflectionClass($class);
            $instance = call_user_func(array($reflectionClass, 'newInstanceArgs'), $args);
        }

        // if instance is not an object, throw exception
        if (!is_object($instance)) {
            throw new \DomainException("Plugin '$name' could not be instancated!");
        }

        // register plugin instance
        $this->plugins[$name] = $instance;
        return $instance;
    }

}