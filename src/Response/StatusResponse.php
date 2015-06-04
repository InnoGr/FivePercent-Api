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
 * Status response
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class StatusResponse extends Response
{
    /**
     * @var bool
     */
    protected $status;

    /**
     * Construct
     *
     * @param bool  $status
     * @param int   $statusCode
     * @param array $headers
     */
    public function __construct($status, $statusCode = 200, array $headers = [])
    {
        $this->status = (bool) $status;

        parent::__construct(['status' => $this->status ? 'ok' : 'fail' ], $statusCode, $headers);
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return ['status' => $this->status ? 'ok' : 'fail' ];
    }

    /**
     * Is ok
     *
     * @return bool
     */
    public function isOk()
    {
        return $this->status;
    }

    /**
     * Is fail
     *
     * @return bool
     */
    public function isFail()
    {
        return !$this->status;
    }
}
