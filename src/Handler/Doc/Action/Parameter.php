<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler\Doc\Action;

/**
 * Request parameter docs
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Parameter implements \Serializable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $required;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * Construct
     *
     * @param string $name
     * @param string $type
     * @param bool   $required
     * @param string $description
     * @param mixed  $default
     */
    public function __construct($name, $type, $required, $description, $default)
    {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
        $this->required = (bool) $required;
        $this->default = $default;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Is required
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get default
     *
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
            $this->name,
            $this->type,
            $this->required,
            $this->description,
            $this->default
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        list (
            $this->name,
            $this->type,
            $this->required,
            $this->description,
            $this->default
        ) = unserialize($serialized);
    }
}
