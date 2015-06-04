<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Doc\Formatter;

use FivePercent\Component\Api\Handler\Doc\Handler\Handler;

/**
 * All registry manager should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface FormatterRegistryInterface
{
    const FORMAT_JSON_RPC = 'json-rpc';

    /**
     * Get formatter
     *
     * @param string $format
     *
     * @return FormatterInterface
     */
    public function getFormatter($format);

    /**
     * Has formatter
     *
     * @param string $format
     *
     * @return bool
     */
    public function hasFormatter($format);

    /**
     * Render handler documentation
     *
     * @param Handler $handler
     * @param string  $format
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(Handler $handler, $format);
}
