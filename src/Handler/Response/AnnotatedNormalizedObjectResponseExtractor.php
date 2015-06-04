<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler\Response;

use FivePercent\Component\Api\SMD\Action\ActionInterface;
use FivePercent\Component\Api\SMD\CallableResolver\CallableInterface;
use FivePercent\Component\ModelNormalizer\Normalizer\Annotated\Metadata\MetadataFactoryInterface;
use FivePercent\Component\Reflection\Reflection;

/**
 * Extract response object from normalized metadata
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class AnnotatedNormalizedObjectResponseExtractor extends ObjectResponseExtractor
{
    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * Construct
     *
     * @param MetadataFactoryInterface $metadataFactory
     */
    public function __construct(MetadataFactoryInterface $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * {@inheritDoc}
     */
    protected function getPropertiesForClass(ActionInterface $action, CallableInterface $callable, $class)
    {
        if (!$this->metadataFactory->supportsClass($class)) {
            return [];
        }

        $metadata = $this->metadataFactory->loadMetadata($class);
        $reflectionClass = Reflection::loadClassReflection($class);

        $properties = [];

        foreach ($metadata->getProperties() as $propertyName => $property) {
            $properties[] = $reflectionClass->getProperty($propertyName);
        }

        return $properties;
    }
}
