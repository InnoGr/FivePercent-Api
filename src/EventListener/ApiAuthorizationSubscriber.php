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
use FivePercent\Component\ObjectSecurity\ObjectSecurityAuthorizationCheckerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Api authorization.
 * For use this subscriber, the package "fivepercent/object-security" must be installed.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ApiAuthorizationSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectSecurityAuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Construct
     *
     * @param ObjectSecurityAuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(ObjectSecurityAuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Authorize and authenticate on API method
     *
     * @param ActionDispatchEvent $event
     */
    public function authorize(ActionDispatchEvent $event)
    {
        $callable = $event->getCallable();

        if (!$callable->isMethod() && !$callable->isMethodStatic()) {
            // Native function or \Closure
            return;
        }

        $class = $callable->getReflection()->getDeclaringClass()->getName();
        $method = $callable->getReflection()->getName();
        $parameters = $event->getParameters();
        $action = $event->getAction();

        foreach ($action->getSecurityGroups() as $group) {
            $authorized = $this->authorizationChecker
                ->isGrantedMethodCall($class, $method, $parameters, [], $group);

            if (!$authorized) {
                throw new AccessDeniedException();
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
                ['authorize', 64]
            ]
        ];
    }
}
