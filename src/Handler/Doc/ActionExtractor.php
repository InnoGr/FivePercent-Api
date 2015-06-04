<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler\Doc;

use FivePercent\Component\Api\Handler\Doc\Action\Action;
use FivePercent\Component\Api\Handler\Parameter\ParameterExtractorInterface;
use FivePercent\Component\Api\Handler\Response\ResponseExtractorInterface;
use FivePercent\Component\Api\SMD\Action\ActionInterface;
use FivePercent\Component\Api\SMD\CallableResolver\CallableResolverInterface;
use phpDocumentor\Reflection\DocBlock;

/**
 * Base action doc Extractor
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ActionExtractor implements ActionExtractorInterface
{
    /**
     * @var CallableResolverInterface
     */
    private $callableResolver;

    /**
     * @var ParameterExtractorInterface
     */
    private $parameterExtractor;

    /**
     * @var ResponseExtractorInterface
     */
    private $responseExtractor;

    /**
     * Construct
     *
     * @param CallableResolverInterface   $callableResolver
     * @param ParameterExtractorInterface $parameterExtractor
     * @param ResponseExtractorInterface  $responseExtractor
     */
    public function __construct(
        CallableResolverInterface $callableResolver,
        ParameterExtractorInterface $parameterExtractor = null,
        ResponseExtractorInterface $responseExtractor = null
    ) {
        $this->callableResolver = $callableResolver;
        $this->parameterExtractor = $parameterExtractor;
        $this->responseExtractor = $responseExtractor;
    }

    /**
     * {@inheritDoc}
     */
    public function extractAction(ActionInterface $action)
    {
        $callable = $this->callableResolver->resolve($action);

        if ($this->parameterExtractor) {
            $parameters = $this->parameterExtractor->extract($action, $callable);
        } else {
            $parameters = [];
        }

        $response = null;

        if ($this->responseExtractor) {
            $response = $this->responseExtractor->extractResponse($action, $callable);
        }

        $reflection = $callable->getReflection();
        $docBlock = new DocBlock($reflection);

        $description = $docBlock->getShortDescription();
        $actionDoc = new Action($action->getName(), $description, $parameters, $response);

        return $actionDoc;
    }
}
