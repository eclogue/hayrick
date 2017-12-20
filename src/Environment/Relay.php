<?php
/**
 * @license   MIT
 * @copyright Copyright (c) 2017
 * @author    : bugbear
 * @date      : 2017/3/10
 * @time      : 下午1:04
 */
namespace Hayrick\Environment;

use InvalidArgumentException;
use Hayrick\Http\Stream;

class Relay
{
    public $server = [];

    public $cookie = [];

    public $files = [];

    public $headers = [];

    public $query = [];

    public $request;

    public $body;



    public static function createFromSwoole($request): Relay
    {
        $relay = new self();
        $relay->server = $request->server ?? [];
        $relay->cookie = $request->cookie ?? [];
        $relay->files = $request->files ?? [];
        $relay->query = $request->get ?? [];
        $relay->headers = $request->header ?? [];
        $stream = fopen('php://temp', 'w+');
        $source = $request->rawContent();
        if ($source) {
            fwrite($stream, $source);
        }

        if (!isset($relay->server['http_host']) && isset($relay->headers['http_host'])) {
            $relay->server['http_host'] = $relay->headers['https_host'];
        }

        $relay->body = new Stream($stream);

        return $relay;
    }

    public static function createFromGlobal(): Relay
    {
        $relay = new self();
        $relay->server = array_change_key_case($_SERVER, CASE_LOWER);
        $relay->cookie = array_change_key_case($_COOKIE, CASE_LOWER);
        $relay->files = array_change_key_case($_FILES, CASE_LOWER);
        $relay->query = $_GET;
        if (!function_exists('getallheaders')) {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $key = strtolower(str_replace('_', ' ', substr($name, 5)));
                    $key = str_replace(' ', '-', $key);
                    $headers[$key] = $value;
                }
            }

            $relay->headers = $headers;
        }

        if (!isset($relay->server['http_host']) && isset($relay->header['http_host'])) {
            $relay->server['http_host'] = $relay->header['https_host'];
        }

        $stream = fopen('php://temp', 'w+');
        stream_copy_to_stream(fopen('php://input', 'r'), $stream);
        rewind($stream);
        $relay->body = new Stream($stream);

        return $relay;
    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __call($name, $arguments)
    {
        if (is_callable([$this->request, $name], true)) {
            return call_user_func_array([$this->request, $name], $arguments);
        } else {
            $message = 'Call undefined function of ' . get_class($this->request);
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * @param $name
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function __get($name)
    {
        if (property_exists($this->request, $name)) {
            return $this->request->$name;
        } else {
            $message = 'Try to get illegal property `%s` of %s';
            $message = sprintf($message, $name, get_class($this->request));
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * @param null $parser body parser
     * @return mixed
     */
    public function getBody($parser = null)
    {
        if (is_array($parser)) {
            return call_user_func_array($parser, [$this->body]);
        } elseif (is_callable($parser)) {
            return $parser($this->body);
        } else {
            return $this->body;
        }
    }

}