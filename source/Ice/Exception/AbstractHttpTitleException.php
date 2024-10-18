<?php

namespace Ice\Exception;

use Ice\Core\Exception;

abstract class AbstractHttpTitleException extends Exception
{
    public function __construct()
    {
        parent::__construct($this->getErrorMessage());
    }

    abstract function getStatusCode(): int;
    abstract function getErrorTitle(): string;
    abstract function getErrorMessage(): string;
}