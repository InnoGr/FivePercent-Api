<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\SMD;

use FivePercent\Component\Api\SMD\Loader\LoaderInterface;

/**
 * Base service manager
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ActionManager implements ActionManagerInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var \FivePercent\Component\Api\SMD\Action\ActionCollection
     */
    private $actionCollection;

    /**
     * Construct
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * {@inheritDoc}
     */
    public function getActions()
    {
        if (null !== $this->actionCollection) {
            return $this->actionCollection;
        }

        $this->actionCollection = $this->loader->loadActions();

        return $this->actionCollection;
    }

    /**
     * {@inheritDoc}
     */
    public function getAction($name)
    {
        return $this->getActions()->getAction($name);
    }

    /**
     * {@inheritDoc}
     */
    public function hasAction($name)
    {
        return $this->getActions()->hasAction($name);
    }
}
