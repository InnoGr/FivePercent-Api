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
use FivePercent\Component\Api\Doc\Formatter\JsonRpc\JsonRpcFormatter;
use FivePercent\Component\Api\Doc\FormatterNotFoundException;

/**
 * Base formatter registry
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class FormatterRegistry implements FormatterRegistryInterface
{
    /**
     * @var array|FormatterInterface[]
     */
    private $formatters;

    /**
     * Add formatter
     *
     * @param string             $format
     * @param FormatterInterface $formatter
     *
     * @return FormatterRegistry
     */
    public function addFormatter($format, FormatterInterface $formatter)
    {
        $this->formatters[$format] = $formatter;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFormatter($format)
    {
        if (!isset($this->formatters[$format])) {
            throw new FormatterNotFoundException(sprintf(
                'Not found formatter with key "%s".',
                $format
            ));
        }

        return $this->formatters[$format];
    }

    /**
     * {@inheritDoc}
     */
    public function hasFormatter($format)
    {
        return isset($this->formatters[$format]);
    }

    /**
     * {@inheritDoc}
     */
    public function render(Handler $handler, $format)
    {
        return $this->getFormatter($format)->render($handler);
    }

    /**
     * Create default registry
     *
     * @return FormatterRegistry
     */
    public static function createDefault()
    {
        /** @var FormatterRegistry $registry */
        $registry = new static();

        $registry->addFormatter(self::FORMAT_JSON_RPC, new JsonRpcFormatter());

        return $registry;
    }
}
