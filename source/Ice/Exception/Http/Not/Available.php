<?php

namespace Ice\Exception;

class Http_Read_Unavaialbe extends HttpTitle
{
    const TITLE = 'Чтение не доступно';
    const HTTP_CODE = 400;
    const HTTP_MESSAGE = 'Forbidden';

    public function getHttpMessage()
    {
        return self::HTTP_MESSAGE;
    }
}