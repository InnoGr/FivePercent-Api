<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Server;

use Symfony\Component\HttpFoundation\Request as SfRequest;

/**
 * All API servers should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ServerInterface
{
    /**
     * Is supported
     *
     * @param SfRequest $request
     *
     * @return bool
     */
    public function isSupported(SfRequest $request);

    /**
     * Handle symfony request
     *
     * @param SfRequest $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(SfRequest $request);
}
