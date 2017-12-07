<?php

namespace Hayrick\Http;

use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use InvalidArgumentException;

class Response extends Message implements ResponseInterface
{

    protected $response;

    /*
     * @var array
     * */
    protected $headers;

    /*
     * @var integer
     * */
    protected $statusCode = 200;

    /*
     * @var body
     * */
    protected $body;

    protected $reasonPhrase;

    protected $content;


    public function __construct()
    {
        $this->headers = new Header();
        $this->body = new Stream(fopen('php://temp', 'r+'));
    }


    public function __clone()
    {
        $this->headers = clone $this->headers;
    }

    public function prepare(Request $request)
    {

    }

    public function write($data)
    {
        $this->getBody()->write($data);

        return $this;
    }


    /**
     * set content-type = json,and response json
     * @param array $data
     * @return ResponseInterface|static
     */
    public function json(array $data)
    {
        $data = json_encode($data);
        $response = $this->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($data);

        return $response;
    }

    /*
     * finish request
     * Note: This method is not part of the PSR-7 standard.
     *
     * @param mix $data
     * @return object
     * */
    public function send($data = [])
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $this->getBody()->write($data);

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return (string) $this->getBody();
    }


    // ===================== PSR-7 standard ===================== //

    public function withStatus($code, $reasonPhrase = '')
    {
        if (!is_string($reasonPhrase) && !method_exists($reasonPhrase, '__toString')) {
            throw new InvalidArgumentException('ReasonPhrase must be a string');
        }

        $clone = clone $this;
        $clone->statusCode = $code;
        if ($reasonPhrase === '' && isset(Header::$messages[$code])) {
            $reasonPhrase = Header::$messages[$code];
        }

        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }


    /**
     * set response header
     *
     * @param string $field
     * @param string $value
     * @return Response
     */
    public function withHeader($field, $value)
    {
        $clone = clone $this;
        $clone->headers->setHeader($field, $value);

        return $clone;
    }

    /*
     * get all response headers
     * */
    public function getHeaders()
    {
        return $this->headers->getHeaders();
    }


    /*
     * get header by key
     * */
    public function getHeader($key, $default = null)
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : $default;
    }


    public function __toString()
    {
        return (string)$this->content;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }


    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {

        if ($this->reasonPhrase) {
            return $this->reasonPhrase;
        }
        if (isset(Header::$messages[$this->statusCode])) {
            return Header::$messages[$this->statusCode];
        }
        return '';
    }

}