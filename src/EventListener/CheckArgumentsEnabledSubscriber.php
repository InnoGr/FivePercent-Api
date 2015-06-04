<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\EventListener;

use FivePercent\Component\Api\Event\ActionDispatchEvent;
use FivePercent\Component\Api\ApiEvents;
use FivePercent\Component\EnabledChecker\EnabledCheckerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Check arguments enabled
 * For use this subscriber, the package "fivepercent/enabled-checker" must be installed.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class CheckArgumentsEnabledSubscriber implements EventSubscriberInterface
{
    /**
     * @var EnabledCheckerInterface
     */
    private $enabledChecker;

    /**
     * Construct
     *
     * @param EnabledCheckerInterface $enabledChecker
     */
    public function __construct(EnabledCheckerInterface $enabledChecker)
    {
        $this->enabledChecker = $enabledChecker;
    }

    /**
     * {@inheritDoc}
     */
    public function checkArgumentsEnabled(ActionDispatchEvent $event)
    {
        if (!$event->getAction()->isCheckEnabled()) {
            // Disable check enabled.
            return;
        }

        $parameters = $event->getParameters();

        foreach ($parameters as $parameter) {
            if (is_object($parameter) && $this->enabledChecker->isSupported($parameter)) {
                $this->enabledChecker->check($parameter);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ApiEvents::ACTION_PRE_DISPATCH => [
                ['checkArgumentsEnabled', 32]
            ]
        ];
    }
}
