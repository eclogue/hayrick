<?php
/**
 * @license MIT
 * @copyright Copyright (c) 2017
 * @author: bugbear
 * @date: 2017/9/28
 * @time: 下午8:31
 */

namespace Hayrick\Environment;

use InvalidArgumentException;
use Hayrick\Http\Header;
use Psr\Http\Message\ResponseInterface;

class Reply
{

    protected $content = [];

    protected $headers = [];

    protected $file = '';

    protected $finish = false;

    protected $statusCode = 200;

    public function __construct()
    {
        $this->header = new Header();
    }

    /**
     * set response header
     *
     * @param string $key
     * @param string $value
     * @return array
     */
    public function header(string $key, string $value): array
    {
        $this->header->setHeader($key, $value);

        return $this->header->getHeaders();
    }

    /**
     * set response body data
     *
     * @param array $data
     * @return array
     */
    public function body(string $data): string
    {
        $this->content = $data;

        return $this->content;
    }

    /**
     * set response file
     *
     * @param string $file
     * @return string
     */
    public function sendFile(string $file): string
    {
        $this->file = $file;

        return $this->file;
    }

    public function status(int $status = 200)
    {
        $this->header->setStatus($status);

        return $this;
    }

    public function getHeaders()
    {
        return $this->header->getHeaders();
    }


    public function send(ResponseInterface $response)
    {
        if (!headers_sent()) {
            // Status
            header(sprintf(
                'HTTP/%s %s %s',
                $this->header->getProtocol(),
                $this->header->getStatusCode(),
                $this->header->getMessage()
            ));

            // Headers
            foreach ($this->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
        }

        // Body
        if (!$response->getBody()) {
            $body = $response->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }

            $chunkSize = 4096; // @todo
            $contentLength  = $response->getHeaderLine('Content-Length');
            if (!$contentLength) {
                $contentLength = $body->getSize();
            }

            if (isset($contentLength)) {
                $amountToRead = $contentLength;
                while ($amountToRead > 0 && !$body->eof()) {
                    $data = $body->read(min($chunkSize, $amountToRead));
                    echo $data;

                    $amountToRead -= strlen($data);

                    if (connection_status() != CONNECTION_NORMAL) {
                        break;
                    }
                }
            } else {
                while (!$body->eof()) {
                    echo $body->read($chunkSize);
                    if (connection_status() != CONNECTION_NORMAL) {
                        break;
                    }
                }
            }
        }
    }

}