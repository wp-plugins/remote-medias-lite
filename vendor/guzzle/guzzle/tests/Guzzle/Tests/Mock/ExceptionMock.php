<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Mock;

class ExceptionMock
{
    public function __construct()
    {
        throw new \Exception('Oh no!');
    }
}
