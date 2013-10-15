<?php
namespace App\Api;

use Guzzle\Common\Exception\RuntimeException;
use Guzzle\Http\Message\Response;

class HttpJsonResponse
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var ResponseError[]
     */
    private $errors = [];

    /**
     * @param Response $httpResponse
     */
    public function __construct(Response $httpResponse)
    {
        $this->parse($httpResponse);
    }

    protected function parse(Response $httpResponse)
    {
        if ($httpResponse->isError()) {
            $this->errors[] = new ResponseError(
                $httpResponse->getReasonPhrase(),
                $httpResponse->getStatusCode()
            );
        } else {
            $this->parseData($httpResponse);
        }
    }

    protected function parseData(Response $httpResponse)
    {
        try {
            $this->data = $httpResponse->json();
            if (!is_array($this->data)) {
                $this->data = ['result' => $this->data];
            }
        } catch (RuntimeException $e) {
            $this->errors[] = new ResponseError(
                $e->getMessage(),
                $e->getCode()
            );
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return (bool) $this->errors;
    }

    /**
     * @return $this
     * @throws ResponseException
     */
    public function throwErrors()
    {
        if ($this->hasErrors()) {
            throw new ResponseException($this->getErrors());
        }

        return $this;
    }
}