<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\SMD\CallableResolver;

/**
 * Callable system.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface CallableInterface
{
    const MODE_FUNCTION         = 1;
    const MODE_METHOD           = 2;
    const MODE_METHOD_STATIC    = 3;

    /**
     * Get reflection
     *
     * @return \ReflectionFunction|\ReflectionMethod
     */
    public function getReflection();

    /**
     * Get object
     *
     * @return object|null
     */
    public function getObject();

    /**
     * Get mode
     *
     * @see MODE_* constants
     *
     * @return int
     */
    public function getMode();

    /**
     * Is method
     *
     * @return bool
     */
    public function isMethod();

    /**
     * Is method static
     *
     * @return bool
     */
    public function isMethodStatic();

    /**
     * Is function
     *
     * @return bool
     */
    public function isFunction();

    /**
     * Apply with arguments
     *
     * @param array $arguments
     *
     * @return mixed
     */
    public function apply(array $arguments = []);
}
