<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler\Doc;

use FivePercent\Component\Api\Handler\HandlerInterface;

/**
 * All doc Extractors should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ExtractorInterface
{
    /**
     * Generate documentation for handler
     *
     * @param HandlerInterface $handler
     *
     * @return Handler\Handler
     */
    public function extract(HandlerInterface $handler);
}
