<?php

namespace Ice\Exception;

class HttpTitleException_Reader_Vkr_Forbidden extends HttpTitleException
{
    public function getStatusCode(): int
    {
        return 400;
    }

    public function getErrorTitle(): string
    {
        return 'Чтение недоступно';
    }

    public function getErrorMessage(): string
    {
        return 'Чтение недоступно. Данная ВКР изъята из фондов ЭБС';
    }
}