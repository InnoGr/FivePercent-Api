<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Handler\Response;

use FivePercent\Component\Api\SMD\Action\ActionInterface;
use FivePercent\Component\Api\SMD\CallableResolver\CallableInterface;

/**
 * All response extractors should implement this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ResponseExtractorInterface
{
    /**
     * Extract response
     *
     * @param ActionInterface   $action
     * @param CallableInterface $callable
     *
     * @return \FivePercent\Component\Api\Handler\Doc\Action\Response
     */
    public function extractResponse(ActionInterface $action, CallableInterface $callable);
}
