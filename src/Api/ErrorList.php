<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Api;

use FivePercent\Component\Api\Handler\HandlerInterface;
use FivePercent\Component\Api\Handler\HandlerRegistryInterface;
use FivePercent\Component\Api\Response\Response;

/**
 * API for view error list of handler
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ErrorList
{
    /**
     * @var HandlerRegistryInterface
     */
    private $handlerRegistry;

    /**
     * @var string
     */
    private $key;

    /**
     * Construct
     *
     * @param HandlerRegistryInterface $registry
     * @param string                   $key
     */
    public function __construct(HandlerRegistryInterface $registry, $key)
    {
        $this->handlerRegistry = $registry;
        $this->key = $key;
    }

    /**
     * Get all errors
     *
     * @return Response
     */
    public function getErrors()
    {
        $errors = $this->handlerRegistry->getHandler($this->key)
            ->getErrors()
            ->getErrors();

        return new Response($errors);
    }
}
