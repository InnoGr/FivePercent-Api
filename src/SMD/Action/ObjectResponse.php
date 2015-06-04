<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\SMD\Action;

/**
 * Object response
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ObjectResponse
{
    /**
     * @var string
     */
    private $class;

    /**
     * Construct
     *
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
