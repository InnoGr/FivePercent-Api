<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler\Doc\Action;

/**
 * Action documentation
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Action implements \Serializable
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var Parameter[]
     */
    protected $parameters;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Construct
     *
     * @param string            $name
     * @param string            $description
     * @param array|Parameter[] $parameters
     * @param Response          $response
     */
    public function __construct($name, $description, array $parameters = [], Response $response = null)
    {
        $this->name = $name;
        $this->description = $description;
        $this->parameters = $parameters;
        $this->response = $response;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get request
     *
     * @return array|Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get response
     *
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([
            $this->name,
            $this->description,
            $this->parameters,
            $this->response
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->name,
            $this->description,
            $this->parameters,
            $this->response
        ) = unserialize($serialized);
    }
}
