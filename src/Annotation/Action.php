<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Annotation;

/**
 * Indicate of API action
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Action
{
    /**
     * Action name. As example: "system.ping"
     *
     * @var string @Required
     */
    public $name;

    /**
     * Security groups. As example: {"Api", "Create"}
     * The package "fivepercent/object-security" must be installed.
     *
     * @var array
     */
    public $securityGroups = ['Default'];

    /**
     * Validation groups. As example: {"Update", "EmailUnique"}
     *
     * @var array
     */
    public $validationGroups = ['Default'];

    /**
     * Required mapping group.
     * The package "fivepercent/object-mapper" must be installed.
     *
     * @var string
     */
    public $requestMappingGroup = 'Default';

    /**
     * Use strict (type) validation before default validation
     *
     * @var bool
     */
    public $useStrictValidation = true;

    /**
     * Check enabled for all input parameters (Method arguments)
     * The package "fivepercent/enabled-checker" must be installed.
     *
     * @var bool
     */
    public $checkEnabled = true;

    /**
     * Response definition. The response extractor should support this parameter and read
     * response definition from here.
     *
     * @var ActionResponse|string
     */
    public $response;

    /**
     * Construct
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value']) && count($values) == 1) {
            $this->name = $values['value'];
        } else {
            if (!empty($values['response'])) {
                $response = $values['response'];

                if (is_object($response)) {
                    $this->response = $response;
                } else {
                    $this->response = new ActionResponse();
                    $this->response->class = $response;
                }

                unset ($values['response']);
            }

            foreach ($values as $key => $value) {
                $this->{$key} = $value;
            }
        }
    }
}
