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
 * All formatter should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface FormatterInterface
{
    /**
     * Render documentation.
     *
     * @param Handler $handlerDoc
     *
     * @return mixed
     */
    public function format(Handler $handlerDoc);

    /**
     * Render
     *
     * @param Handler $handlerDoc
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render(Handler $handlerDoc);
}
