<?php

namespace App\Exceptions;

interface JsonApiException
{
    public function makeResponseData():array;
}
