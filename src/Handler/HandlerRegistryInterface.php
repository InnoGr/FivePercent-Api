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
 * All handler managers should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface HandlerRegistryInterface
{
    /**
     * Get handler keys
     *
     * @return array
     */
    public function getHandlerKeys();

    /**
     * Get handler
     *
     * @param string $handler
     *
     * @return HandlerInterface
     *
     * @throws \FivePercent\Component\Api\Exception\HandlerNotFoundException
     */
    public function getHandler($handler);
}
