<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\EventListener;

use FivePercent\Component\Api\Event\ActionViewEvent;
use FivePercent\Component\Api\ApiEvents;
use FivePercent\Component\Api\Response\EmptyResponse;
use FivePercent\Component\Api\Response\ObjectResponse;
use FivePercent\Component\Api\Response\Response;
use FivePercent\Component\Exception\UnexpectedTypeException;
use FivePercent\Component\ModelNormalizer\Exception\UnsupportedClassException as NormalizerUnsupportedObjectException;
use FivePercent\Component\ModelNormalizer\ModelNormalizerManagerInterface;
use FivePercent\Component\ModelTransformer\Exception\UnsupportedClassException as TransformerUnsupportedObjectException;
use FivePercent\Component\ModelTransformer\ModelTransformerManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Transform response, if necessary
 * For use this subscriber, the packages "fivepercent/model-transformer" and "fivepercent/model-normalizer"
 * must be installed.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ResponseTransformableSubscriber implements EventSubscriberInterface
{
    /**
     * @var ModelTransformerManagerInterface
     */
    private $transformerManager;

    /**
     * @var ModelNormalizerManagerInterface
     */
    private $normalizerManager;

    /**
     * Construct
     *
     * @param ModelTransformerManagerInterface $transformerManager
     * @param ModelNormalizerManagerInterface  $normalizerManager
     */
    public function __construct(
        ModelTransformerManagerInterface $transformerManager,
        ModelNormalizerManagerInterface $normalizerManager
    ) {
        $this->normalizerManager = $normalizerManager;
        $this->transformerManager = $transformerManager;
    }

    /**
     * Transform object response
     *
     * @param ActionViewEvent $event
     */
    public function transformObjectResponse(ActionViewEvent $event)
    {
        $data = $event->getData();

        if ($data instanceof ObjectResponse) {
            $response = $this->doTransformObjectResponse($data);
            $event->setResponse($response);
        }
    }

    /**
     * Transform object. Try create a object response via object in response.
     *
     * @param ActionViewEvent $event
     */
    public function transformObject(ActionViewEvent $event)
    {
        $data = $event->getData();

        if (is_array($data)) {
            $data = new \ArrayObject($data);
        }

        if ($this->normalizerManager->supports($data) && !$this->transformerManager->supports($data)) {
            $objectResponse = new ObjectResponse($data);
            $objectResponse->removeActionTransform();

            $response = $this->doTransformObjectResponse($objectResponse);
            $event->setResponse($response);
        }

        if ($this->transformerManager->supports($data)) {
            $objectResponse = new ObjectResponse($data);

            $response = $this->doTransformObjectResponse($objectResponse);
            $event->setResponse($response);
        }
    }

    /**
     * Process transform object response
     *
     * @param ObjectResponse $objectResponse
     *
     * @return Response
     */
    private function doTransformObjectResponse(ObjectResponse $objectResponse)
    {
        $responseData = $objectResponse;

        if ($objectResponse->isActionTransform()) {
            try {
                $responseData = $this->transformerManager->transform(
                    $responseData->getObject(),
                    $objectResponse->getTransformerContext()
                );

                if (!is_object($responseData)) {
                    throw UnexpectedTypeException::create($responseData, 'object');
                }
            } catch (TransformerUnsupportedObjectException $e) {
                throw new \RuntimeException(sprintf(
                    'Can not transform object with class "%s".',
                    get_class($objectResponse)
                ), 0, $e);
            }
        }

        try {
            $responseData = $this->normalizerManager->normalize(
                $responseData instanceof ObjectResponse ? $responseData->getObject() : $responseData,
                $objectResponse->getNormalizerContext()
            );

            if (!is_array($responseData)) {
                throw UnexpectedTypeException::create($responseData, 'array');
            }
        } catch (NormalizerUnsupportedObjectException $e) {
            throw new \RuntimeException(sprintf(
                'Can not normalize object with class "%s".',
                get_class($responseData)
            ), 0, $e);
        }

        if ($objectResponse->isEmptyResponse()) {
            $response = new EmptyResponse($responseData, $objectResponse->getHttpStatusCode());
        } else {
            $response = new Response($responseData, $objectResponse->getHttpStatusCode());
        }

        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ApiEvents::ACTION_VIEW => [
                ['transformObjectResponse'],
                ['transformObject']
            ]
        ];
    }
}
