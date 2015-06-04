<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Doc;

use FivePercent\Component\Api\Handler\Doc\ExtractorInterface;
use FivePercent\Component\Api\Doc\Formatter\FormatterRegistryInterface;
use FivePercent\Component\Api\Handler\HandlerInterface;

/**
 * Generate documentation for API methods
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class DocGenerator implements DocGeneratorInterface
{
    /**
     * @var ExtractorInterface
     */
    private $extractor;

    /**
     * @var FormatterRegistryInterface
     */
    private $formatterRegistry;

    /**
     * Construct
     *
     * @param ExtractorInterface         $extractor
     * @param FormatterRegistryInterface $formatterRegistry
     */
    public function __construct(ExtractorInterface $extractor, FormatterRegistryInterface $formatterRegistry)
    {
        $this->extractor = $extractor;
        $this->formatterRegistry = $formatterRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function hasFormatter($key)
    {
        return $this->formatterRegistry->hasFormatter($key);
    }

    /**
     * {@inheritDoc}
     */
    public function generate(HandlerInterface $handler, $outputFormat)
    {
        $documentation = $this->extractor->extract($handler);

        return $this->formatterRegistry->render($documentation, $outputFormat);
    }
}
