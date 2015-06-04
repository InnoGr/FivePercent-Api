<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Doc\Formatter\JsonRpc;

use FivePercent\Component\Api\Handler\Doc\Action\Action;
use FivePercent\Component\Api\Doc\Formatter\FormatterInterface;
use FivePercent\Component\Api\Handler\Doc\Action\ResponseProperty;
use FivePercent\Component\Api\Handler\Doc\Handler\Handler;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * JSON-RPC documentation formatter
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class JsonRpcFormatter implements FormatterInterface
{
    /**
     * {@inheritDoc}
     */
    public function format(Handler $handlerDoc)
    {
        $json = [
            'transport' => 'POST',
            'envelope' => 'JSON-RPC-2.0',
            'contentType' => 'application/json-rpc',
            'SMDVersion' => '2.0',
            'description' => null,
            'methods' => []
        ];

        foreach ($handlerDoc->getActions() as $action) {
            $actionInfo = $this->formatAction($action);

            $json['methods'][$action->getName()] = $actionInfo;
        }

        return $json;
    }

    /**
     * {@inheritDoc}
     */
    public function render(Handler $handlerDoc)
    {
        $json = $this->format($handlerDoc);

        return new JsonResponse($json);
    }

    /**
     * Format action
     *
     * @param Action $action
     *
     * @return array
     */
    private function formatAction(Action $action)
    {
        $info = [
            'envelope' => 'JSON-RPC-2.0',
            'transport' => 'POST',
            'name' => $action->getName(),
            'description' => $action->getDescription(),
            'parameters' => []
        ];

        foreach ($action->getParameters() as $parameter) {
            $parameterInfo = [
                'name' => $parameter->getName(),
                'type' => $parameter->getType(),
                'description' => $parameter->getDescription(),
                'required' => $parameter->isRequired()
            ];

            if (!$parameter->isRequired()) {
                $parameterInfo['default'] = $parameter->getDefault();
            }

            $info['parameters'][] = $parameterInfo;
        }

        if ($action->getResponse()) {
            $response = $action->getResponse();

            $info['response'] = $response->toArray();
        }

        return $info;
    }

    /**
     * Format action response property
     *
     * @param ResponseProperty $responseProperty
     *
     * @return array
     */
    public function formatActionResponseProperty(ResponseProperty $responseProperty)
    {
        $info = [
            'name' => $responseProperty->getName(),
            'type' => $responseProperty->getType(),
            'description' => $responseProperty->getDescription()
        ];

        if ($responseProperty->isObject() || $responseProperty->isCollection()) {
            foreach ($responseProperty->getChild() as $resProperty) {
                $info['properties'][$resProperty->getName()] = $this->formatActionResponseProperty($resProperty);
            }
        }

        return $info;
    }
}
