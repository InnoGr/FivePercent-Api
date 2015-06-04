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
 * Response data
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 * @author Dmitry Krasun <krasun.net@gmail.com>
 */
class Response
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var array|ResponseProperty[]
     */
    private $properties = [];
    /**
     * @var string
     */
    private $className;

    /**
     * Construct
     *
     * @param string                   $type
     * @param string                   $className
     * @param array|ResponseProperty[] $properties
     */
    public function __construct($type, $className, array $properties = [])
    {
        $this->type = $type;
        $this->className = $className;
        $this->properties = $properties;
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
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * Get properties
     *
     * @return array|ResponseProperty[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Get response as array
     *
     * @return array
     */
    public function toArray()
    {
        $response = [
            'type' => $this->type,
            'className' => $this->className
        ];

        if (count($this->properties)) {
            $response['properties'] = [];

            foreach ($this->properties as $property) {
                $response['properties'][$property->getName()] = $property->toArray();
            }
        }

        return $response;
    }
}
