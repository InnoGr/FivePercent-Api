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
 * All API services should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ActionInterface extends \Serializable
{
    /**
     * Get service name
     *
     * @return string
     */
    public function getName();

    /**
     * Get request mapping group
     *
     * @return string
     */
    public function getRequestMappingGroup();

    /**
     * Get security group
     *
     * @return array
     */
    public function getSecurityGroups();

    /**
     * Get validation group
     *
     * @return array
     */
    public function getValidationGroups();

    /**
     * Is use strict validation
     *
     * @return bool
     */
    public function isStrictValidation();

    /**
     * Is check parameters of enabled
     *
     * @return bool
     */
    public function isCheckEnabled();

    /**
     * Get response
     *
     * @return mixed
     */
    public function getResponse();
}
