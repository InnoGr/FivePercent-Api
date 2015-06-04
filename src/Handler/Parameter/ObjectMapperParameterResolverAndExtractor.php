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
use FivePercent\Component\Api\Request\RequestInterface;
use FivePercent\Component\Api\SMD\Action\ActionInterface;
use FivePercent\Component\Api\SMD\CallableResolver\CallableInterface;
use FivePercent\Component\Converter\Exception\ConverterNotFoundException;
use FivePercent\Component\Converter\Converters\ORM\Exception\InvalidArgumentException;
use FivePercent\Component\Converter\Parameter\ParameterConverterManagerInterface;
use FivePercent\Component\Converter\Property\PropertyConverterManagerInterface;
use FivePercent\Component\Exception\ViolationListException;
use FivePercent\Component\ObjectMapper\ObjectMapperInterface;
use FivePercent\Component\Reflection\Reflection;
use FivePercent\Component\VarTagValidator\VarTagValidatorInterface;
use phpDocumentor\Reflection\DocBlock;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Request object parameter resolver
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ObjectMapperParameterResolverAndExtractor implements ParameterResolverInterface, ParameterExtractorInterface
{
    /**
     * @var ObjectMapperInterface
     */
    private $objectMapper;

    /**
     * @var ParameterConverterManagerInterface
     */
    private $parameterConverter;

    /**
     * @var PropertyConverterManagerInterface
     */
    private $propertyConverter;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug;

    /**
     * Construct
     *
     * @param ObjectMapperInterface              $objectMapper
     * @param ParameterConverterManagerInterface $parameterConverter
     * @param PropertyConverterManagerInterface  $propertyConverter
     * @param ValidatorInterface                 $validator
     * @param LoggerInterface                    $logger
     * @param bool                               $debug
     */
    public function __construct(
        ObjectMapperInterface $objectMapper,
        ParameterConverterManagerInterface $parameterConverter = null,
        PropertyConverterManagerInterface $propertyConverter = null,
        ValidatorInterface $validator = null,
        LoggerInterface $logger = null,
        $debug = false
    ) {
        $this->objectMapper = $objectMapper;
        $this->parameterConverter = $parameterConverter;
        $this->propertyConverter = $propertyConverter;
        $this->validator = $validator;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ActionInterface $action, CallableInterface $callable, array $inputArguments)
    {
        $arguments = array();

        $requestParameter = null;
        $parameters = $callable->getReflection()->getParameters();

        foreach ($parameters as $parameter) {
            if (isset($arguments[$parameter->getName()])) {
                throw new \RuntimeException(sprintf(
                    'Many parameters with one name "%s" in method "%s".',
                    $parameter->getName(),
                    Reflection::getCalledMethod($callable->getReflection())
                ));
            }

            if ($class = $parameter->getClass()) {
                if ($class->implementsInterface('FivePercent\Component\Api\Request\RequestInterface')) {
                    if ($requestParameter) {
                        throw new \LogicException(sprintf(
                            'The request already declared in parameter with name "%s" for method "%s".',
                            $requestParameter,
                            Reflection::getCalledMethod($callable->getReflection())
                        ));
                    }
                    $requestParameter = $parameter->getName();

                    $request = $this->resolveRequest($action, $callable, $inputArguments, $parameter);

                    $arguments[$parameter->getName()] = $request;

                    continue;
                }
            }

            if ($parameter->isDefaultValueAvailable()) {
                $arguments[$parameter->getName()] = $parameter->getDefaultValue();
            } else {
                $arguments[$parameter->getName()] = null;
            }
        }

        if (!$requestParameter && $this->debug) {
            if ($this->logger) {
                $this->logger->warning(sprintf(
                    'Not found request parameter in arguments for callable "%s". ' .
                    'Request parameter should implement "FivePercent\Component\Api\Request\RequestInterface"',
                    Reflection::getCalledMethod($callable->getReflection())
                ));
            }
        }

        // Convert parameters
        if ($this->parameterConverter) {
            try {
                $arguments = $this->parameterConverter->convertArguments(
                    $callable->getReflection(),
                    $arguments,
                    ParameterConverterManagerInterface::GROUP_DEFAULT
                );
            } catch (ConverterNotFoundException $e) {
                if ($this->logger) {
                    $this->logger->warning(sprintf(
                        'Could not convert arguments with message: %s.',
                        rtrim($e->getMessage(), '.')
                    ));
                }
            }
        }

        return $arguments;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(ActionInterface $action, CallableInterface $callable)
    {
        /** @var \ReflectionClass $requestClass */
        $requestClass = null;

        $inputParameters = $callable->getReflection()->getParameters();

        foreach ($inputParameters as $parameter) {
            if ($class = $parameter->getClass()) {
                if ($class->implementsInterface('FivePercent\Component\Api\Request\RequestInterface')) {
                    $requestClass = $class;
                    break;
                }
            }
        }

        if (!$requestClass) {
            return [];
        }

        $requestParameters = [];
        $requestObject = $requestClass->newInstance();

        $objectMetadata = $this->objectMapper->getMetadataFactory()
            ->load($requestObject, $action->getRequestMappingGroup());

        if (!$objectMetadata) {
            // Can not load metadata for object
            return [];
        }

        foreach ($objectMetadata->getProperties() as $property) {
            if (!$property->reflection) {
                $property->reflection = new \ReflectionProperty(
                    $requestClass->getName(),
                    $property->getPropertyName()
                );
            }

            $docBlock = new DocBlock($property->reflection);

            $content = $docBlock->getShortDescription();
            $typeTags = $docBlock->getTagsByName('var');
            $type = null;

            if ($typeTags) {
                /** @var \phpDocumentor\Reflection\DocBlock\Tag\VarTag $typeTag */
                $typeTag = array_pop($typeTags);
                $type = $typeTag->getType();

                //$type = $this->formatPHPType($type);
            }

            $defaultValue = null;
            if ($property->reflection->isDefault()) {
                if (!$property->reflection->isPublic()) {
                    $property->reflection->setAccessible(true);
                }

                $defaultValue = $property->reflection->getValue($requestObject);
            }

            $parameter = new Parameter(
                $property->getFieldName(),
                $type,
                $this->isPropertyRequired($property->reflection, $action->getValidationGroups()),
                $content,
                $defaultValue
            );

            $requestParameters[$property->getFieldName()] = $parameter;
        }

        return $requestParameters;
    }

    /**
     * Resolve request parameter
     *
     * @param ActionInterface      $action
     * @param CallableInterface    $callable
     * @param array                $inputArguments
     * @param \ReflectionParameter $parameter
     *
     * @return object
     *
     * @throws \Exception
     */
    private function resolveRequest(
        ActionInterface $action,
        CallableInterface $callable,
        array $inputArguments,
        \ReflectionParameter $parameter
    ) {
        $class = $parameter->getClass();

        if ($class->isInterface()) {
            throw new \RuntimeException(sprintf(
                'Could not create instance via interface for parameter "%s" in method "%s". ' .
                'You must set the class for type hinting.',
                $parameter->getName(),
                Reflection::getCalledMethod($callable->getReflection())
            ));
        }

        if ($class->isAbstract()) {
            throw new \RuntimeException(sprintf(
                'Could not create instance via abstract class for parameter "%s" in method "%s". ' .
                'You must set the real class for type hinting.',
                $parameter->getName(),
                Reflection::getCalledMethod($callable->getReflection())
            ));
        }

        /** @var RequestInterface $request */
        $request = $class->newInstance();

        // Map arguments
        $this->objectMapper->map($request, $inputArguments, $action->getRequestMappingGroup());

        // First step validation: Strict mode
        if ($this->validator && $action->isStrictValidation()) {
            $this->strictRequestValidate($request);
        }

        // Second step validation: Base validation
        if ($this->validator && $action->getValidationGroups()) {
            $violationList = $this->validator->validate($request, null, $action->getValidationGroups());

            if (count($violationList)) {
                throw ViolationListException::create($violationList);
            }
        }

        // Convert request properties
        if ($this->propertyConverter) {
            try {
                $this->propertyConverter->convertProperties(
                    $request,
                    PropertyConverterManagerInterface::GROUP_DEFAULT
                );
            } catch (InvalidArgumentException $e) {
                $constraintViolation = new ConstraintViolation(
                    $e->getMessage(),
                    null,
                    [],
                    null,
                    $e->getName(),
                    $e->getInvalidValue()
                );

                $constraintViolationList = new ConstraintViolationList([$constraintViolation]);

                throw ViolationListException::create($constraintViolationList);
            } catch (ConverterNotFoundException $e) {
                if ($this->logger) {
                    $this->logger->warning(sprintf(
                        'Could not convert properties with message: %s.',
                        rtrim($e->getMessage(), '.')
                    ));
                }

                // Nothing action
            }
        }

        return $request;
    }

    /**
     * Strict request validation
     *
     * @param RequestInterface $request
     *
     * @throws ViolationListException
     */
    private function strictRequestValidate(RequestInterface $request)
    {
        $requestMetadata = $this->validator->getMetadataFor($request);

        $useStrictGroup = false;

        // Search constraints by group "Strict"
        if ($requestMetadata instanceof ClassMetadata) {
            foreach ($requestMetadata->getConstrainedProperties() as $propertyName) {
                $propertyMetadata = $requestMetadata->getPropertyMetadata($propertyName);

                if (count($propertyMetadata)) {
                    $propertyMetadata = array_shift($propertyMetadata);
                } else {
                    continue;
                }

                $constraintsInStrictGroup = $propertyMetadata->findConstraints('Strict');

                if (count($constraintsInStrictGroup)) {
                    $useStrictGroup = true;
                    break;
                }
            }
        }

        if ($useStrictGroup) {
            // The any properties in request class have a "Strict" group validation
            $violationList = $this->validator->validate($request, null, ['Strict']);

            if (count($violationList)) {
                throw ViolationListException::create($violationList);
            }

            return;
        }

        if ($this->validator instanceof VarTagValidatorInterface) {
            $violationList = $this->validator->validateObjectByVarTags($request);

            if (count($violationList)) {
                throw ViolationListException::create($violationList);
            }
        }
    }

    /**
     * Is required property
     *
     * @param \ReflectionProperty $property
     * @param array               $groups
     *
     * @return bool
     */
    private function isPropertyRequired(\ReflectionProperty $property, array $groups)
    {
        if (!$this->validator) {
            // Can not check...
            return true;
        }

        $metadata = $this->validator->getMetadataFor($property->getDeclaringClass()->getName());

        if (!$metadata instanceof ClassMetadata) {
            return true;
        }

        $propertyMetadata = $metadata->getPropertyMetadata($property->getName());

        if ($propertyMetadata) {
            // @todo: merge all metadata?
            $propertyMetadata = array_pop($propertyMetadata);

            foreach ($groups as $group) {
                $constraints = $propertyMetadata->findConstraints($group);

                foreach ($constraints as $constraint) {
                    if ($constraint instanceof NotBlank) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
