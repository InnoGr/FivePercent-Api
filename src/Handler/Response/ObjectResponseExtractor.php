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

use FivePercent\Component\Api\Exception\ResponseExtractException;
use FivePercent\Component\Api\Exception\ResponseExtractorNotSupportException;
use FivePercent\Component\Api\Handler\Doc\Action\Response;
use FivePercent\Component\Api\Handler\Doc\Action\ResponseProperty;
use FivePercent\Component\Api\SMD\Action\ActionInterface;
use FivePercent\Component\Api\SMD\Action\ObjectResponse;
use FivePercent\Component\Api\SMD\CallableResolver\CallableInterface;
use FivePercent\Component\Reflection\Reflection;
use phpDocumentor\Reflection\DocBlock;

/**
 * Extract response from object (mapped in action)
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ObjectResponseExtractor implements ResponseExtractorInterface
{
    /**
     * @var array
     */
    private $phpTypeAliases = [];

    /**
     * Add php type alias
     *
     * @param string $phpType
     * @param string $alias
     *
     * @return ObjectResponseExtractor
     */
    public function addPhpTypeAlias($phpType, $alias)
    {
        $this->phpTypeAliases[$phpType] = $alias;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function extractResponse(ActionInterface $action, CallableInterface $callable)
    {
        $response = $action->getResponse();

        if (!$response) {
            return null;
        }

        if (!is_object($response) || !$response instanceof ObjectResponse) {
            throw new ResponseExtractorNotSupportException(sprintf(
                'The response must be ObjectResponse for extract, but "%s" given.'
            ));
        }

        $class = $response->getClass();

        if (substr($class, strlen($class) - 2) == '[]') {
            $type = 'collection';
            $class = substr($class, 0, strlen($class) - 2);
        } else {
            $type = 'object';
        }

        if (!$class) {
            return null;
        }

        if (!class_exists($class)) {
            throw new ResponseExtractException(sprintf(
                'Can not extract response from class "%s" for callable "%s". Class not found.',
                $class,
                Reflection::getCalledMethod($callable->getReflection())
            ));
        }

        // Get properties from object
        $properties = $this->getPropertiesForClass($action, $callable, $class);

        $responseProperties = [];

        foreach ($properties as $property) {
            $responseProperties[] = $this->extractResponseProperty($action, $callable, $property);
        }

        return new Response($type, $class, $responseProperties);
    }

    /**
     * Extract response property
     *
     * @param ActionInterface     $action
     * @param CallableInterface   $callable
     * @param \ReflectionProperty $property
     *
     * @return ResponseProperty
     */
    protected function extractResponseProperty(
        ActionInterface $action,
        CallableInterface $callable,
        \ReflectionProperty $property
    ) {
        $docBlock = new DocBlock($property);
        $description = $docBlock->getShortDescription();
        $type = 'mixed';
        $child = null;

        $varTags = $docBlock->getTagsByName('var');

        if (count($varTags)) {
            /** @var \phpDocumentor\Reflection\DocBlock\Tag\VarTag $varTag */
            $varTag = $varTags[0];
            $varTypes = $varTag->getTypes();
            $varType = null;

            if (count($varTypes)) {
                $varType = array_pop($varTypes);
            }

            switch (true) {
                case 'int' == $varType:
                case 'integer' == $varType:
                    $type = 'int';
                    break;

                case 'string' == $varType:
                    $type = 'string';
                    break;

                case 'float' == $varType:
                case 'double' == $varType:
                    $type = 'float';
                    break;

                case 'boolean' == $varType:
                case 'bool' == $varType:
                    $type = 'bool';
                    break;

                case 'DateTime' == ltrim($varType, '\\'):
                    $type = 'datetime';
                    break;

                default:
                    if ($varType && class_exists($varType)) {
                        $type = 'object';
                        $childProperties = $this->getPropertiesForClass($action, $callable, $varType);
                        $child = [];

                        foreach ($childProperties as $childProperty) {
                            $child[$childProperty->getName()] = $this->extractResponseProperty(
                                $action,
                                $callable,
                                $childProperty
                            );
                        }

                        break;
                    } else if ($varType && substr($varType, strlen($varType) - 2) == '[]') {
                        $childClass = substr($varType, 0, strlen($varType) - 2);
                        $type = 'collection';
                        $childProperties = $this->getPropertiesForClass($action, $callable, $childClass);
                        $child = [];

                        foreach ($childProperties as $childProperty) {
                            $child[$childProperty->getName()] = $this->extractResponseProperty(
                                $action,
                                $callable,
                                $childProperty
                            );
                        }

                        break;
                    }

                    $this->tryFormatPhpType($varTag, $varType);
            }
        }

        $responseProperty = new ResponseProperty($property->getName(), $type, $description, $child);

        return $responseProperty;
    }

    /**
     * Get properties for class
     *
     * @param ActionInterface   $action
     * @param CallableInterface $callable
     * @param string            $class
     *
     * @return \ReflectionProperty[]
     */
    protected function getPropertiesForClass(ActionInterface $action, CallableInterface $callable, $class)
    {
        return Reflection::getClassProperties($class);
    }

    /**
     * Try format php type
     *
     * @param DocBlock\Tag\VarTag $varTag
     * @param string              $varType
     *
     * @return string
     */
    protected function tryFormatPhpType(DocBlock\Tag\VarTag $varTag, $varType)
    {
        if (isset($this->phpTypeAliases[$varType])) {
            return $this->phpTypeAliases[$varType];
        }

        return $varType;
    }
}
