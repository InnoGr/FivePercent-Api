<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler\Builder;

use FivePercent\Component\Api\Exception\AlreadyBuildedException;
use FivePercent\Component\Api\Handler\BaseHandler;
use FivePercent\Component\Api\Handler\Doc\ActionExtractor;
use FivePercent\Component\Api\Handler\Doc\Extractor;
use FivePercent\Component\Api\Handler\Parameter\MethodParameterResolverAndExtractor;
use FivePercent\Component\Api\Handler\Parameter\ParameterExtractorInterface;
use FivePercent\Component\Api\Handler\Parameter\ParameterResolverInterface;
use FivePercent\Component\Api\SMD\ActionManager;
use FivePercent\Component\Api\SMD\CallableResolver\CallableResolverInterface;
use FivePercent\Component\Api\SMD\CallableResolver\ChainResolver;
use FivePercent\Component\Api\SMD\CallableResolver\CallableResolver;
use FivePercent\Component\Api\SMD\Loader\ChainLoader;
use FivePercent\Component\Api\SMD\Loader\CallableLoader;
use FivePercent\Component\Api\SMD\Loader\LoaderInterface;
use FivePercent\Component\Error\ErrorFactoryInterface;
use FivePercent\Component\Error\Errors;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Base handler builder
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class HandlerBuilder implements HandlerBuilderInterface
{
    /**
     * @var \FivePercent\Component\Api\Handler\HandlerInterface
     */
    private $handler;

    /**
     * @var \FivePercent\Component\Api\Handler\Doc\ExtractorInterface
     */
    private $docExtractor;

    /**
     * @var \FivePercent\Component\Error\ErrorFactoryInterface[]
     */
    protected $errorFactories = [];

    /**
     * @var \FivePercent\Component\Error\Errors
     */
    protected $errors;

    /**
     * @var array|CallableResolverInterface[]
     */
    protected $callableResolvers = [];

    /**
     * @var ChainResolver
     */
    protected $callableResolver;

    /**
     * @var array|LoaderInterface[]
     */
    protected $actionLoaders = [];

    /**
     * @var ChainLoader
     */
    protected $actionLoader;

    /**
     * @var ActionManager
     */
    protected $actionManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ParameterResolverInterface
     */
    protected $parameterResolver;

    /**
     * @var ParameterExtractorInterface
     */
    protected $parameterExtractor;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Add error factory
     *
     * @param ErrorFactoryInterface $errorFactory
     *
     * @return HandlerBuilder
     */
    public function addErrorFactory(ErrorFactoryInterface $errorFactory)
    {
        $this->errorFactories[spl_object_hash($errorFactory)] = $errorFactory;

        return $this;
    }

    /**
     * Add closure handle
     *
     * @return CallableLoader
     *
     * @throws AlreadyBuildedException
     */
    public function addCallableHandle()
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $loader = new CallableLoader();
        $resolver = new CallableResolver();

        $this->addCallableResolver($resolver);
        $this->addActionLoader($loader);

        return $loader;
    }

    /**
     * Add callable resolver
     *
     * @param CallableResolverInterface $callableResolver
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function addCallableResolver(CallableResolverInterface $callableResolver)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->callableResolvers[spl_object_hash($callableResolver)] = $callableResolver;

        return $this;
    }

    /**
     * Add action loader
     *
     * @param LoaderInterface $loader
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function addActionLoader(LoaderInterface $loader)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->actionLoaders[spl_object_hash($loader)] = $loader;

        return $this;
    }

    /**
     * Set event dispatcher
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Set parameter resolver
     *
     * @param ParameterResolverInterface $resolver
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function setParameterResolver(ParameterResolverInterface $resolver)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->parameterResolver = $resolver;

        return $this;
    }

    /**
     * Set parameter extractor
     *
     * @param ParameterExtractorInterface $extractor
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function setParameterExtractor(ParameterExtractorInterface $extractor)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->parameterExtractor = $extractor;

        return $this;
    }

    /**
     * Set logger
     *
     * @param LoggerInterface $logger
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function setLogger(LoggerInterface $logger)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->logger = $logger;

        return $this;
    }

    /**
     * Set debug
     *
     * @param bool $debug
     *
     * @return HandlerBuilder
     *
     * @throws AlreadyBuildedException
     */
    public function setDebug($debug)
    {
        if ($this->handler) {
            throw new AlreadyBuildedException('The handler already builded.');
        }

        $this->debug = (bool) $debug;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function buildHandler()
    {
        if ($this->handler) {
            return $this->handler;
        }

        // Create action loader and action manager
        $this->actionLoader = $this->createActionLoader();
        $this->actionManager = $this->createActionManager();

        // Create callable resolver
        $this->callableResolver = $this->createCallableResolver();

        // Create errors system
        $this->errors = $this->createErrors();

        if (!$this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcher();
        }

        if (!$this->parameterResolver) {
            $this->parameterResolver = $this->createParameterResolver();
        }

        if (!$this->parameterExtractor && $this->parameterResolver instanceof ParameterExtractorInterface) {
            $this->parameterExtractor = $this->parameterResolver;
        }

        // Create handler
        $handler = new BaseHandler(
            $this->actionManager,
            $this->callableResolver,
            $this->parameterResolver,
            $this->eventDispatcher,
            $this->errors
        );

        return $handler;
    }

    /**
     * {@inheritDoc}
     */
    public function buildDocExtractor()
    {
        if ($this->docExtractor) {
            return $this->docExtractor;
        }

        $actionExtractor = new ActionExtractor($this->callableResolver, $this->parameterExtractor);
        $this->docExtractor = new Extractor($actionExtractor);

        return $this->docExtractor;
    }

    /**
     * Create parameter resolver
     *
     * @return MethodParameterResolverAndExtractor
     */
    protected function createParameterResolver()
    {
        return new MethodParameterResolverAndExtractor();
    }

    /**
     * Create action loader
     *
     * @return ChainLoader
     */
    protected function createActionLoader()
    {
        return new ChainLoader($this->actionLoaders);
    }

    /**
     * Create action manager
     *
     * @return ActionManager
     */
    protected function createActionManager()
    {
        return new ActionManager($this->actionLoader);
    }

    /**
     * Create callable resolver
     *
     * @return ChainResolver
     */
    protected function createCallableResolver()
    {
        return new ChainResolver($this->callableResolvers);
    }

    /**
     * Create error
     *
     * @return Errors
     */
    protected function createErrors()
    {
        return new Errors($this->errorFactories);
    }
}
