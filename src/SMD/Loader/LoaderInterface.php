<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\SMD\Loader;

/**
 * All SMD loaders should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface LoaderInterface
{
    /**
     * Get all actions
     *
     * @return \FivePercent\Component\Api\SMD\Action\ActionCollectionInterface
     */
    public function loadActions();
}
