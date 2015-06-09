<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Server\JsonRpc;

use FivePercent\Component\Api\Exception\ApiExceptionInterface;
use FivePercent\Component\Api\Response\EmptyResponseInterface;
use FivePercent\Component\Api\Response\ResponseInterface;
use FivePercent\Component\Api\Server\AbstractServer;
use FivePercent\Component\Api\Server\Exception\MissingHttpContentException;
use FivePercent\Component\Api\Server\JsonRpc\Exception\InvalidIdException;
use FivePercent\Component\Api\Server\JsonRpc\Exception\InvalidMethodException;
use FivePercent\Component\Api\Server\JsonRpc\Exception\InvalidParametersException;
use FivePercent\Component\Api\Server\JsonRpc\Exception\MissingMethodException;
use FivePercent\Component\Api\SMD\Exception\ActionNotFoundException;
use FivePercent\Component\Exception\JsonParseException;
use FivePercent\Component\Exception\ViolationListException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request as SfRequest;
use Symfony\Component\HttpFoundation\Response as SfResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * JSON RPC Server
 *
 * For more information about JSON-RPC, please see http://www.jsonrpc.org/specification
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class JsonRpcServer extends AbstractServer
{
    const JSON_RPC_VERSION              = '2.0';

    /** JSON-RPC errors */
    const ERROR_APPLICATION             = -32500;
    const INVALID_REQUEST               = -32600;
    const METHOD_NOT_FOUND              = -32601;
    const INVALID_PARAMS                = -32602;
    const INTERNAL_ERROR                = -32603;

    /**
     * Internal parameter for control request id
     *
     * @var int
     */
    private $requestId;

    /**
     * {@inheritDoc}
     */
    public function isSupported(SfRequest $request)
    {
        $contentType = $request->getContentType();

        return in_array($contentType, ['application/json', 'application/json-rpc']);
    }

    /**
     * {@inheritDoc}
     */
    protected function isHandleDocumentation(SfRequest $request)
    {
        return $request->getMethod() === SfRequest::METHOD_GET;
    }

    /**
     * {@inheritDoc}
     */
    protected function doHandle(SfRequest $request)
    {
        $this->requestId = null;

        if ($request->getMethod() === SfRequest::METHOD_OPTIONS) {
            // OPTIONS query
            return $this->processOptionsQuery($request);
        } if ($request->getMethod() === SfRequest::METHOD_POST) {
            // Process method
            return $this->processApiMethod($request);
        } elseif ($request->getMethod() === SfRequest::METHOD_GET) {
            if ($this->debug && $request->query->get('_method') === SfRequest::METHOD_POST) {
                return $this->processApiQuery($request->query->all());
            }
        }

        throw new MethodNotAllowedHttpException(['GET', 'POST'], sprintf(
            'The method "%s" not allowed.',
            $request->getMethod()
        ));
    }

    /**
     * {@inheritDoc}
     */
    protected function doHandleException(SfRequest $request, \Exception $exception)
    {
        $errors = $this->handler->getErrors();

        switch (true) {
            case $exception instanceof JsonParseException:
            case $exception instanceof MissingHttpContentException:
                return $this->createErrorResponse(self::INVALID_REQUEST);

            case $exception instanceof MissingMethodException:
            case $exception instanceof InvalidIdException:
            case $exception instanceof InvalidMethodException:
            case $exception instanceof InvalidParametersException:
                return $this->createErrorResponse(self::INVALID_PARAMS);

            case $exception instanceof AccessDeniedException:
                $code = $errors->hasException($exception) ? $errors->getExceptionCode($exception) : 0;

                return $this->createErrorResponse($code);

            case $exception instanceof ActionNotFoundException:
                return $this->createErrorResponse(self::METHOD_NOT_FOUND);

            case $exception instanceof ViolationListException:
                return $this->createViolationErrorResponse($exception);

            case $exception instanceof ApiExceptionInterface:
                /** @var \Exception $exception */
                return $this->createErrorResponse($exception->getCode(), $exception->getMessage());

            case $errors->hasException($exception):
                /** @var \Exception $exception */
                $code = $errors->getExceptionCode($exception);
                $message = $exception->getMessage();

                if (!$message) {
                    $errorMessages = $errors->getErrors();
                    $message = isset($errorMessages[$code]) ? $errorMessages[$code] : 'Error';
                }

                return $this->createErrorResponse($code, $message);

            case $exception instanceof MethodNotAllowedHttpException:
                return new Response('', 405, [
                    'Allow' => 'GET, POST, HEAD, OPTIONS',
                    'Content-Type' => 'text/plain'
                ]);

            default:
                return $this->createErrorResponse(self::ERROR_APPLICATION);

        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultDocumentationFormat()
    {
        return 'json-rpc';
    }

    /**
     * {@inheritDoc}
     */
    protected function getContentType()
    {
        return 'application/json';
    }

    /**
     * Process OPTIONS query
     *
     * @param SfRequest $request
     *
     * @return Response
     */
    private function processOptionsQuery(SfRequest $request)
    {
        $response = new Response('', 200, [
            'Allow' => 'GET, POST, HEAD, OPTIONS',
            'Content-Type' => 'text/plain'
        ]);

        return $response;
    }

    /**
     * Process API method
     *
     * @param SfRequest $request
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    private function processApiMethod(SfRequest $request)
    {
        // Try parse JSON
        $content = $request->getContent();

        if (!$content) {
            throw new MissingHttpContentException('Missing HTTP content.');
        }

        $query = @json_decode($content, true);

        if (false === $query) {
            throw JsonParseException::create(json_last_error());
        }

        return $this->processApiQuery($query);
    }

    /**
     * Process api query
     *
     * @param array $query
     *
     * @return JsonResponse
     *
     * @throws \Exception
     */
    private function processApiQuery(array $query)
    {
        $query += array(
            'params' => array(),
            'id' => null
        );

        if (empty($query['method'])) {
            throw new MissingMethodException('Missing "method" parameter in query.');
        }

        if ($query['id'] !== null && !is_scalar($query['id'])) {
            throw new InvalidIdException('The "id" parameter must be a scalar.');
        }

        if (!is_scalar($query['method'])) {
            throw new InvalidMethodException('The "method" parameter must be a scalar.');
        }

        if (!is_array($query['params'])) {
            throw new InvalidParametersException('Input parameters must be a array.');
        }

        $this->requestId = $query['id'];

        $apiResponse = $this->handler->handle($query['method'], $query['params']);

        if ($apiResponse instanceof EmptyResponseInterface) {
            if ($apiResponse instanceof ResponseInterface) {
                return new SfResponse('', $apiResponse->getHttpStatusCode(), $apiResponse->getHeaders()->all());
            } else {
                return new SfResponse('');
            }
        }

        $data = array(
            'jsonrpc' => self::JSON_RPC_VERSION,
            'result' => $apiResponse->getData(),
            'id' => $query['id']
        );

        return new JsonResponse($data, $apiResponse->getHttpStatusCode(), $apiResponse->getHeaders()->all());
    }

    /**
     * Create error response
     *
     * @param integer $code
     * @param string  $message
     * @param array   $data
     *
     * @return JsonResponse
     */
    private function createErrorResponse($code, $message = null, array $data = [])
    {
        if (!$message) {
            $messages = $this->handler->getErrors()->getErrors();
            $message = isset($messages[$code]) ? $messages[$code] : 'Error';
        }

        $json = [
            'jsonrpc' => self::JSON_RPC_VERSION,
            'error' => [
                'code' => $code,
                'message' => $message
            ],
            'id' => $this->requestId
        ];

        if ($data) {
            $json['error']['data'] = $data;
        }

        return new JsonResponse($json, 200);
    }

    /**
     * Create violation error response
     *
     * @param ViolationListException $exception
     *
     * @return JsonResponse
     */
    public function createViolationErrorResponse(ViolationListException $exception)
    {
        $errorData = [];

        /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
        foreach ($exception->getViolationList() as $violation) {
            $errorData[$violation->getPropertyPath()] = $violation->getMessage();
        }

        // Try get code from errors storage via exception
        $errors = $this->handler->getErrors();
        $code = $errors->hasException($exception) ? $errors->getExceptionCode($exception) : 0;

        return $this->createErrorResponse($code, null, $errorData);
    }
}
