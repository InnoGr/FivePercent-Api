<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Server;

/**
 * All server registry should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ServerRegistryInterface
{
    /**
     * Get server
     *
     * @param string $key
     *
     * @return ServerInterface
     *
     * @throws Exception\ServerNotFoundException
     */
    public function getServer($key);
}
