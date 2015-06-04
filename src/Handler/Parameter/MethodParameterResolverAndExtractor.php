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

use FivePercent\Component\Api\Handler\Doc\Action\Parameter;
use FivePercent\Component\Api\SMD\Action\ActionInterface;
use FivePercent\Component\Api\SMD\CallableResolver\CallableInterface;

/**
 * Request object parameter resolver
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * @todo: correct extract data with use php doc block.
 */
class MethodParameterResolverAndExtractor implements ParameterResolverInterface, ParameterExtractorInterface
{
    /**
     * {@inheritDoc}
     */
    public function resolve(ActionInterface $action, CallableInterface $callable, array $inputParameters)
    {
        $methodParameters = $callable->getReflection()->getParameters();
        $parameters = [];

        foreach ($methodParameters as $methodParameter) {
            $parameterName = $methodParameter->getName();

            if (isset($inputParameters[$parameterName])) {
                $parameters[] = $inputParameters[$parameterName];
            } else {
                if ($methodParameter->isOptional()) {
                    $parameters[] = $methodParameter->getDefaultValue();
                } else {
                    $parameters[] = null;
                }
            }
        }

        return $parameters;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(ActionInterface $action, CallableInterface $callable)
    {
        $reflection = $callable->getReflection();
        $methodParameters = $reflection->getParameters();
        $parameters = [];

        foreach ($methodParameters as $methodParameter) {
            $defaultValue = null;

            if ($methodParameter->isOptional()) {
                $defaultValue = $methodParameter->getDefaultValue();
            }

            $name = $methodParameter->getName();

            $parameters[] = new Parameter(
                $name,
                'string',
                !$methodParameter->isOptional(),
                null,
                $defaultValue
            );
        }

        return $parameters;
    }
}
