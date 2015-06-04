<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\SMD\Loader;

use FivePercent\Component\Api\SMD\Action\ActionCollection;

/**
 * Chain SMD loader
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ChainLoader implements LoaderInterface
{
    /**
     * @var array|LoaderInterface[]
     */
    private $loaders = [];

    /**
     * Construct
     *
     * @param array|LoaderInterface[] $loaders
     */
    public function __construct(array $loaders = [])
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * Add loader
     *
     * @param LoaderInterface $loader
     *
     * @return ChainLoader
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[spl_object_hash($loader)] = $loader;

        return $this;
    }

    /**
     * Get all actions
     *
     * @return \FivePercent\Component\Api\SMD\Action\ActionCollectionInterface
     */
    public function loadActions()
    {
        $actions = new ActionCollection();

        foreach ($this->loaders as $loader) {
            $childActions = $loader->loadActions();

            $actions->addActions($childActions);
        }

        return $actions;
    }
}
