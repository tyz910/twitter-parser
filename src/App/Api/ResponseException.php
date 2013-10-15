<?php
namespace App\Api;

class ResponseException extends \Exception
{
    /**
     * @param ResponseError[] $errors
     */
    public function __construct(array $errors)
    {
        parent::__construct(implode("\r\n", $errors));
    }
}