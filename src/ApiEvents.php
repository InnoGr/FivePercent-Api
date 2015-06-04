<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api;

/**
 * Available API event list
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
final class ApiEvents
{
    /**
     * Allows to override dispatch logic before action invocation.
     *
     * @see \FivePercent\Component\Api\Event\ActionDispatchEvent
     */
    const ACTION_PRE_DISPATCH       = 'fivepercent.api.action.pre_dispatch';

    /**
     * Allows to override dispatch logic after action invocation.
     *
     * @see \FivePercent\Component\Api\Event\ActionDispatchEvent
     */
    const ACTION_POST_DISPATCH      = 'fivepercent.api.action.post_dispatch';

    /**
     * Transform response
     *
     * @see \FivePercent\Component\Api\Event\ActionViewEvent
     */
    const ACTION_VIEW               = 'fivepercent.api.action.view';

    /**
     * Control exception
     *
     * @see \FivePercent\Component\Api\Event\ActionExceptionEvent
     */
    const ACTION_EXCEPTION          = 'fivepercent.api.action.exception';

    /**
     * Disable constructor
     */
    private function __construct()
    {
    }
}
