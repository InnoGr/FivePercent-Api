<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\SMD\Action;

/**
 * Base abstract action
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $securityGroups = [ 'Default' ];

    /**
     * @var array
     */
    protected $validationGroups = [ 'Default' ];

    /**
     * @var string
     */
    protected $requestMappingGroup = 'Default';

    /**
     * @var bool
     */
    protected $strictValidation = true;

    /**
     * @var bool
     */
    protected $checkEnabled = true;

    /**
     * @var mixed
     */
    protected $response = null;

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getValidationGroups()
    {
        return $this->validationGroups;
    }

    /**
     * {@inheritDoc}
     */
    public function getSecurityGroups()
    {
        return $this->securityGroups;
    }

    /**
     * {@inheritDoc}
     */
    public function isStrictValidation()
    {
        return $this->strictValidation;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestMappingGroup()
    {
        return $this->requestMappingGroup;
    }

    /**
     * {@inheritDoc}
     */
    public function isCheckEnabled()
    {
        return $this->checkEnabled;
    }

    /**
     * {@inheritDoc}
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
        $data = [];

        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        return serialize($data);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}
