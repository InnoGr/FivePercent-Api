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
use FivePercent\Component\Api\SMD\CallableResolver\CallableInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Action view event. You must transform data to API response in this event
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ActionViewEvent extends Event
{
    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @var CallableInterface
     */
    private $callable;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * Construct
     *
     * @param ActionInterface   $action
     * @param CallableInterface $callable
     * @param array             $parameters
     * @param mixed             $data
     */
    public function __construct(ActionInterface $action, CallableInterface $callable, array $parameters, $data)
    {
        $this->action = $action;
        $this->callable = $callable;
        $this->parameters = $parameters;
        $this->data = $data;
    }

    /**
     * Get action
     *
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Get callable
     *
     * @return CallableInterface
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set response
     *
     * @param ResponseInterface $response
     *
     * @return ActionViewEvent
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
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
