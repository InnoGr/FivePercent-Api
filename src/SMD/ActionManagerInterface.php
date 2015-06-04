<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\SMD;

use FivePercent\Component\Api\SMD\Action\ActionInterface;

/**
 * All service managers should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ActionManagerInterface
{
    /**
     * Get action by name
     *
     * @param string $name
     *
     * @return ActionInterface
     *
     * @throws Exception\ActionNotFoundException
     */
    public function getAction($name);

    /**
     * Get all actions
     *
     * @return \FivePercent\Component\Api\SMD\Action\ActionCollectionInterface
     */
    public function getActions();

    /**
     * Has action
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasAction($name);
}
