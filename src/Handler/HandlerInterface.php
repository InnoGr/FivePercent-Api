<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler;

/**
 * All API handlers should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface HandlerInterface
{
    /**
     * Get errors
     *
     * @return \FivePercent\Component\Error\Errors
     */
    public function getErrors();

    /**
     * Get actions
     *
     * @return \FivePercent\Component\Api\SMD\Action\ActionCollection|\FivePercent\Component\Api\SMD\Action\ActionInterface[]
     */
    public function getActions();

    /**
     * Handle
     *
     * @param string $method     The method name for call
     * @param array  $parameters Named parameters
     *
     * @return \FivePercent\Component\Api\Response\ResponseInterface
     */
    public function handle($method, array $parameters);
}
