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
 * Response property data
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ResponseProperty
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array|ResponseProperty[]
     */
    private $child;

    /**
     * Construct
     *
     * @param string                    $name
     * @param string                    $type
     * @param string                    $description
     * @param array|ResponseProperty[]  $child
     */
    public function __construct($name, $type, $description, array $child = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->description = $description;
        $this->child = $child;
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
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get child properties (Available only "object")
     *
     * @return array|ResponseProperty[]
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Is object property
     *
     * @return bool
     */
    public function isObject()
    {
        return $this->type == 'object';
    }

    /**
     * Is collection property
     *
     * @return bool
     */
    public function isCollection()
    {
        return $this->type == 'collection';
    }

    /**
     * Get response as array
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description
        ];

        if (null !== $this->child) {
            $data['properties'] = [];

            foreach ($this->child as $child) {
                $data['properties'][$child->getName()] = $child->toArray();
            }
        }

        return $data;
    }
}
