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
 * Action collection
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ActionCollection implements \Iterator, \Countable, \Serializable
{
    /**
     * @var array
     */
    private $actions = [];

    /**
     * Add action
     *
     * @param Action $action
     *
     * @return ActionCollection
     */
    public function addAction(Action $action)
    {
        $this->actions[$action->getName()] = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @param string $action
     *
     * @return Action
     */
    public function getAction($action)
    {
        return $this->actions[$action];
    }

    /**
     * Remove action
     *
     * @param string $action
     *
     * @return ActionCollection
     */
    public function removeAction($action)
    {
        unset ($this->actions[$action]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return key($this->actions) !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        return reset($this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->actions);
    }

    /**
     * {@inheritDoc}
     */
    public function serialize()
    {
        return serialize([$this->actions]);
    }

    /**
     * {@inheritDoc}
     */
    public function unserialize($serialized)
    {
        list (
            $this->actions
        ) = unserialize($serialized);
    }
}
