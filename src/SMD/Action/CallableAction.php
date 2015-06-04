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

use FivePercent\Component\ObjectMapper\Metadata\ObjectMetadata;
use FivePercent\Component\ObjectSecurity\Metadata\Security;

/**
 * Callable action
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class CallableAction extends AbstractAction
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * Construct
     *
     * @param string   $name
     * @param callable $callable
     * @param array    $validationGroups
     * @param array    $securityGroups
     * @param string   $requestMappingGroup
     * @param bool     $strictValidation
     * @param bool     $checkEnabled
     * @param mixed    $response
     */
    public function __construct(
        $name,
        $callable,
        array $validationGroups = ['Default'],
        array $securityGroups = [ Security::DEFAULT_GROUP ],
        $requestMappingGroup = ObjectMetadata::DEFAULT_GROUP,
        $strictValidation = false,
        $checkEnabled = true,
        $response = null
    ) {
        if (!is_callable($callable)) {
            throw new \RuntimeException(sprintf(
                'The callback must be a callable, but "%s" given.',
                gettype($callable)
            ));
        }

        $this->name = $name;
        $this->callable = $callable;
        $this->validationGroups = $validationGroups;
        $this->securityGroups = $securityGroups;
        $this->requestMappingGroup = $requestMappingGroup;
        $this->strictValidation = $strictValidation;
        $this->checkEnabled = $checkEnabled;
        $this->response = $response;
    }

    /**
     * Get callable
     *
     * @return callable
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        if ($this->callable instanceof \Closure) {
            throw new \RuntimeException('Could not serialize \Closure instance.');
        }

        if (is_array($this->callable) && is_object($this->callable[0])) {
            throw new \RuntimeException(sprintf(
                'Could not serialize action, because you use method of object "%s".',
                get_class($this->callable[0])
            ));
        }

        return parent::serialize();
    }
}
