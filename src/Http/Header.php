<?php
/**
 * @license   https://github.com/Init/licese.md
 * @copyright Copyright (c) 2016
 * @author    : bugbear
 * @date      : 2016/11/30
 * @time      : 下午8:02
 */

namespace Hayrick\Http;


class Header
{

    public static $messages = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    public $statusCode = 200;

    public $protocolVersion = '1.1';

    public $headers = [];

    public function __construct($code = 200)
    {
        $this->statusCode = $code;
    }


    public static function defaultHeader()
    {
        return [
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml,application/json;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
            'X-Powered-By' => 'courser',
            'REQUEST_TIME' => time(),
        ];
    }

    /**
     * init headers
     *
     * @param array $headers
     */
    public function init(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * check header
     *
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->headers[$key]);
    }

    /**
     * set statusCode
     *
     * @param $code
     */
    public function setStatus($code)
    {
        $this->statusCode = $code;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * get status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function getHeader(string $name, $default = null)
    {
        return $this->headers[$name] ?? $default;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return 'HTTP/' . $this->protocolVersion;
    }

    /**
     * get response message
     *
     * @return mixed|string
     */
    public function getMessage()
    {
        return static::$messages[$this->statusCode] ?? '';
    }

    /**
     * remove header
     *
     * @param $key
     * @return $this
     */
    public function remove(string $key)
    {
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }

        return $this;
    }
}