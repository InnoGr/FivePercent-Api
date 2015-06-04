<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler\Doc\Handler;

use FivePercent\Component\Api\Handler\Doc\Action\ActionCollection;

/**
 * Handler documentation
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Handler
{
    /**
     * @var ActionCollection
     */
    protected $actions;

    /**
     * Construct
     *
     * @param ActionCollection $actions
     */
    public function __construct(ActionCollection $actions)
    {
        $this->actions = $actions;
    }

    /**
     * Get actions
     *
     * @return ActionCollection
     */
    public function getActions()
    {
        return $this->actions;
    }
}
