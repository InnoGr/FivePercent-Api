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

use Symfony\Component\HttpFoundation\HeaderBag;

/**
 * Base response
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class Response implements ResponseInterface
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var HeaderBag
     */
    protected $headers;

    /**
     * Construct
     *
     * @param mixed $data
     * @param int   $statusCode
     * @param array $headers
     */
    public function __construct($data, $statusCode = 200, array $headers = [])
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers = new HeaderBag($headers);
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getHttpStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
