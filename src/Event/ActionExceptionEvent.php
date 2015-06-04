<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Event;

use FivePercent\Component\Api\Response\ResponseInterface;
use FivePercent\Component\Api\SMD\Action\ActionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Exception event
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ActionExceptionEvent extends Event
{
    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Construct
     *
     * @param ActionInterface|null $action
     * @param \Exception           $exception
     */
    public function __construct(ActionInterface $action = null, \Exception $exception = null)
    {
        $this->action = $action;
        $this->exception = $exception;
    }

    /**
     * Get exception
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Set response
     *
     * @param ResponseInterface $response
     *
     * @return ActionExceptionEvent
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        $this->stopPropagation();

        return $this;
    }

    /**
     * Get response
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Has response
     *
     * @return bool
     */
    public function hasResponse()
    {
        return (bool) $this->response;
    }
}
