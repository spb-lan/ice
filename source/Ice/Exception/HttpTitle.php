<?php

namespace Ice\Exception;

use Ice\Core\Exception;

abstract class HttpTitle extends Exception
{
    const HTTP_CODE = 500;
    const TITLE = '';

    public function __construct($message,
                                $errcontext = [],
                                $previous = null,
                                $errfile = null,
                                $errline = null,
                                $errno = 0)
    {
        parent::__construct($message, $errcontext, $previous, $errfile, $errline, $errno);
    }

    public function getHttpCode(): int
    {
        return static::HTTP_CODE;
    }

    public function getErrorTitle(): string
    {
        return static::TITLE;
    }

    abstract function getHttpMessage();
}