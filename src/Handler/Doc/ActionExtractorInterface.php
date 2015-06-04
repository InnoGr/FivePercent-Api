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

use FivePercent\Component\Api\SMD\Action\ActionInterface;

/**
 * All action doc Extractors should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ActionExtractorInterface
{
    /**
     * Generate documentation for action
     *
     * @param ActionInterface $action
     *
     * @return Action\Action
     */
    public function extractAction(ActionInterface $action);
}
