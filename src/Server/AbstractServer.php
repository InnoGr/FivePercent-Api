<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Server;

use FivePercent\Component\Api\Doc\DocGenerator;
use FivePercent\Component\Api\Doc\DocGeneratorInterface;
use FivePercent\Component\Api\Doc\Formatter\FormatterRegistryInterface;
use FivePercent\Component\Api\Handler\Builder\HandlerBuilderInterface;
use FivePercent\Component\Api\Handler\HandlerInterface;
use Symfony\Component\HttpFoundation\Request as SfRequest;
use Symfony\Component\HttpFoundation\Response as SfResponse;

/**
 * Abstract server
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
abstract class AbstractServer implements ServerInterface
{
    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * @var DocGeneratorInterface
     */
    protected $docGenerator;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * Construct
     *
     * @param HandlerInterface      $handler
     * @param DocGeneratorInterface $docGenerator
     * @param bool                  $debug
     */
    public function __construct(HandlerInterface $handler, DocGeneratorInterface $docGenerator = null, $debug = false)
    {
        $this->handler = $handler;
        $this->docGenerator = $docGenerator;
        $this->debug = $debug;
    }

    /**
     * {@inheritDoc}
     */
    final public function handle(SfRequest $request)
    {
        $this->preHandle();

        if ($this->isHandleDocumentation($request) && !($this->debug && $request->query->has('_method'))) {
            if (!$this->docGenerator) {
                return new SfResponse('Error: not found doc generator.', 500, [
                    'Content-Type' => 'text/plain'
                ]);
            }

            $format = $request->query->get('_format', $this->getDefaultDocumentationFormat());

            if (!$this->docGenerator->hasFormatter($format)) {
                $format = $this->getDefaultDocumentationFormat();
            }

            return $this->docGenerator->generate($this->handler, $format);
        }

        try {
            $response = $this->doHandle($request);
            $this->prepareResponse($response, $request);

            return $response;
        } catch (\Exception $e) {
            if ($this->debug && $request->query->has('_exception')) {
                throw $e;
            }

            $response = $this->doHandleException($request, $e);

            if ($response && $response instanceof SfResponse) {
                if ($this->debug) {
                    $response->headers->add([
                        'X-Exception-Code' => $e->getCode(),
                        'X-Exception-Class' => get_class($e)
                    ]);
                }

                $this->prepareResponse($response, $request);

                return $response;
            }

            return new SfResponse('Critical system error. Please try again.', 500, array(
                'Content-Type' => 'text/plain'
            ));
        }
    }

    /**
     * Get handler
     *
     * @return HandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Create server via handler builder
     *
     * @param HandlerBuilderInterface    $builder
     * @param FormatterRegistryInterface $formatterRegistry
     * @param bool                       $debug
     *
     * @return ServerInterface
     */
    public static function createWithBuilder(
        HandlerBuilderInterface $builder,
        FormatterRegistryInterface $formatterRegistry,
        $debug = false
    ) {
        $handler = $builder->buildHandler();
        $docGenerator = new DocGenerator($builder->buildDocExtractor(), $formatterRegistry);

        return new static($handler, $docGenerator, $debug);
    }

    /**
     * Pre handle action
     */
    protected function preHandle()
    {
    }

    /**
     * Prepare response
     *
     * @param SfResponse $response
     * @param SfRequest  $request
     */
    protected function prepareResponse(SfResponse $response, SfRequest $request)
    {
        $contentType = $this->getContentType();

        if ($contentType && !$response->headers->get('Content-Type')) {
            $response->headers->set('Content-Type', $contentType, true);
        }
    }

    /**
     * Is handle method list
     *
     * @param SfRequest $request
     *
     * @return bool
     */
    protected function isHandleDocumentation(SfRequest $request)
    {
        return false;
    }

    /**
     * Get default documentation format
     *
     * @return string
     */
    abstract protected function getDefaultDocumentationFormat();

    /**
     * Get content type
     *
     * @return string
     */
    abstract protected function getContentType();

    /**
     * Handle process for request
     *
     * @param SfRequest $request
     *
     * @return SfResponse
     */
    abstract protected function doHandle(SfRequest $request);

    /**
     * Handle process for exception
     *
     * @param SfRequest  $request
     * @param \Exception $exception
     *
     * @return SfResponse
     */
    abstract protected function doHandleException(SfRequest $request, \Exception $exception);
}
