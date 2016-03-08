<?php
/*
 * This file is part of the Jentin framework.
 * (c) Steffen Zeidler <sigma_z@sigma-scripts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jentin\Mvc\Response;

/**
 * @author Zeidler
 * @date   08.03.2016
 */
class ResponseCookie
{

    /** @var string */
    private $name;

    /** @var string */
    protected $value = '';

    /** @var int */
    protected $expire = 0;

    /** @var string */
    protected $path = '';

    /** @var string */
    protected $domain = '';

    /** @var bool */
    protected $secure = false;

    /** @var bool */
    protected $httpOnly = false;


    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


    /**
     * @return int
     */
    public function getExpire()
    {
        return $this->expire;
    }


    /**
     * @param int $expire
     * @return $this
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
        return $this;
    }


    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }


    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }


    /**
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }


    /**
     * @param string $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }


    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }


    /**
     * @param boolean $secure
     * @return $this
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;
        return $this;
    }


    /**
     * @return boolean
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }


    /**
     * @param boolean $httpOnly
     * @return $this
     */
    public function setHttpOnly($httpOnly)
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }

}
