<?php
/**
 * @license MIT
 * @copyright Copyright (c) 2018
 * @author: bugbear
 * @date: 2018/3/5
 * @time: 上午12:13
 */
namespace Hayrick\Environment;

use Psr\Http\Message\StreamInterface;

abstract class RelayAbstract
{
    public $server;

    public $headers;

    public $cookie;

    public $files;

    public $body;

    public $query;

    public function getBody()
    {

    }

    public function toArray(): array
    {
        return [
            $this->server,
            $this->headers,
            $this->cookie,
            $this->files,
            $this->body,
            $this->query,
        ];
    }
}