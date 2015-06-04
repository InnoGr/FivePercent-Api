<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Response;

use FivePercent\Component\Exception\UnexpectedTypeException;
use FivePercent\Component\ModelTransformer\ContextInterface as TransformerContextInterface;
use FivePercent\Component\ModelTransformer\Context as TransformerContext;
use FivePercent\Component\ModelNormalizer\ContextInterface as NormalizerContextInterface;
use FivePercent\Component\ModelNormalizer\Context as NormalizerContext;

/**
 * Object response. You can set the transform and normalization context and
 * other options for generate real response.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ObjectResponse
{
    const ACTION_TRANSFORM      = 0b00000001;
    const ACTION_NORMALIZE      = 0b00000010;

    /**
     * @var int
     */
    private $action;

    /**
     * @var object
     */
    private $object;

    /**
     * @var NormalizerContextInterface
     */
    private $normalizerContext;

    /**
     * @var TransformerContextInterface
     */
    private $transformerContext;

    /**
     * @var int
     */
    private $httpStatusCode = 200;

    /**
     * @var bool
     */
    private $emptyResponse = false;

    /**
     * Construct
     *
     * @param object                      $object
     * @param TransformerContextInterface $transformerContext
     * @param NormalizerContextInterface  $normalizerContext
     *
     * @throws UnexpectedTypeException
     */
    public function __construct(
        $object,
        TransformerContextInterface $transformerContext = null,
        NormalizerContextInterface $normalizerContext = null
    ) {
        if (!is_object($object)) {
            throw UnexpectedTypeException::create($object, 'object');
        }

        $this->action = self::ACTION_TRANSFORM | self::ACTION_NORMALIZE;
        $this->object = $object;
    }

    /**
     * Get object
     *
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Add transform action
     *
     * @return ObjectResponse
     */
    public function addActionTransform()
    {
        $this->action = $this->action | self::ACTION_TRANSFORM;

        return $this;
    }

    /**
     * Remove transform action
     *
     * @return ObjectResponse
     */
    public function removeActionTransform()
    {
        $this->action = $this->action & ~self::ACTION_TRANSFORM;

        return $this;
    }

    /**
     * Is transform action
     *
     * @return bool
     */
    public function isActionTransform()
    {
        return $this->action & self::ACTION_TRANSFORM;
    }

    /**
     * Is normalize action
     *
     * @return bool
     */
    public function isActionNormalize()
    {
        return $this->action & self::ACTION_NORMALIZE;
    }

    /**
     * Set normalizer context
     *
     * @param NormalizerContextInterface $context
     *
     * @return ObjectResponse
     */
    public function setNormalizerContext(NormalizerContextInterface $context)
    {
        $this->normalizerContext = $context;

        return $this;
    }

    /**
     * Get normalizer context
     *
     * @return NormalizerContextInterface
     */
    public function getNormalizerContext()
    {
        if ($this->normalizerContext) {
            return $this->normalizerContext;
        }

        return new NormalizerContext();
    }

    /**
     * Set transformer context
     *
     * @param TransformerContextInterface $context
     *
     * @return ObjectResponse
     */
    public function setTransformerContext(TransformerContextInterface $context)
    {
        $this->transformerContext = $context;

        return $this;
    }

    /**
     * Get transformer context
     *
     * @return TransformerContextInterface
     */
    public function getTransformerContext()
    {
        if ($this->transformerContext) {
            return $this->transformerContext;
        }

        return new TransformerContext();
    }

    /**
     * Set http status code
     *
     * @param int $statusCode
     *
     * @return ObjectResponse
     */
    public function setHttpStatusCode($statusCode)
    {
        $this->httpStatusCode = $statusCode;

        return $this;
    }

    /**
     * Get http status code
     *
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * Set empty response
     *
     * @param bool $emptyResponse
     *
     * @return ObjectResponse
     */
    public function setEmptyResponse($emptyResponse)
    {
        $this->emptyResponse = (bool) $emptyResponse;

        return $this;
    }

    /**
     * Get empty response
     *
     * @return bool
     */
    public function isEmptyResponse()
    {
        return $this->emptyResponse;
    }
}
