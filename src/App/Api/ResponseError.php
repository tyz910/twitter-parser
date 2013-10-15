<?php
namespace App\Api;

class ResponseError
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var string|int
     */
    private $code;

    /**
     * @param string $message
     * @param string|int $code
     */
    public function __construct($message = '', $code = 0)
    {
        $this->message = $message;
        $this->code = $code;
    }

    /**
     * @param string|int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string|int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Response Error #%s: %s.', $this->getCode(), $this->getMessage());
    }
}