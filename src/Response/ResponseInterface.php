<?php

/**
 * This file is part of the Api package
 *
 * (c) InnovationGroup
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace FivePercent\Component\Api\Response;

/**
 * All API responses should be implemented of this interface
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ResponseInterface
{
    /**
     * Get data
     *
     * @return mixed
     */
    public function getData();

    /**
     * Get http status code
     *
     * @return int
     */
    public function getHttpStatusCode();

    /**
     * Get headers
     *
     * @return \Symfony\Component\HttpFoundation\HeaderBag
     */
    public function getHeaders();
}
