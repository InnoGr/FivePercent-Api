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
use FivePercent\Component\Cache\CacheInterface;

/**
 * Cached extractor
 * Attention: you must use only one handler for extract, because we not has handler key or another options
 * for indicate handler system!
 *
 * @author Vitaliy Zhuk <zhuk@gmail.com>
 */
class CachedExtractor implements ExtractorInterface
{
    /**
     * @var ExtractorInterface
     */
    private $delegate;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $key;

    /**
     * Construct
     *
     * @param ExtractorInterface $delegate
     * @param CacheInterface     $cache
     * @param string             $key
     */
    public function __construct(ExtractorInterface $delegate, CacheInterface $cache, $key)
    {
        $this->delegate = $delegate;
        $this->cache = $cache;
        $this->key = $key;
    }

    /**
     * {@inheritDoc}
     */
    public function extract(HandlerInterface $handler)
    {
        $cacheKey = $this->generateCacheKey($handler);

        $handlerDoc = $this->cache->get($cacheKey);

        if (!$handlerDoc) {
            $handlerDoc = $this->delegate->extract($handler);
            $this->cache->set($cacheKey, $handlerDoc);
        }

        return $handlerDoc;
    }

    /**
     * Generate cache key for handler
     *
     * @param HandlerInterface $handler
     *
     * @return string
     */
    protected function generateCacheKey(HandlerInterface $handler)
    {
        return 'api.handler.doc.extractor.' . $this->key;
    }
}
