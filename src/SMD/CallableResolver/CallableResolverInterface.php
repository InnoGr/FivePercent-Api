<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\SMD\CallableResolver;

use FivePercent\Component\Api\SMD\Action\ActionInterface;

/**
 * Callable resolver. Resolver callbacks by action
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface CallableResolverInterface
{
    /**
     * Is supported action
     *
     * @param ActionInterface $action
     *
     * @return bool
     */
    public function isSupported(ActionInterface $action);

    /**
     * Get reflection for actions
     *
     * @param ActionInterface $action
     *
     * @return CallableInterface
     */
    public function resolve(ActionInterface $action);
}
