<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler\Parameter;

use FivePercent\Component\Api\SMD\Action\ActionInterface;
use FivePercent\Component\Api\SMD\CallableResolver\CallableInterface;

/**
 * All parameter resolvers should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ParameterResolverInterface
{
    /**
     * Resolve arguments
     *
     * @param ActionInterface   $action
     * @param CallableInterface $callable
     * @param array             $inputParameters
     *
     * @return array
     */
    public function resolve(ActionInterface $action, CallableInterface $callable, array $inputParameters);
}
