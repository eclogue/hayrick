<?php
/**
 * @license MIT
 * @copyright Copyright (c) 2018
 * @author: bugbear
 * @date: 2018/3/5
 * @time: 上午12:17
 */

namespace Hayrick\Environment;

use Psr\Http\Message\ResponseInterface;

interface ReplyInterface
{
    public function end(ResponseInterface $response);
}