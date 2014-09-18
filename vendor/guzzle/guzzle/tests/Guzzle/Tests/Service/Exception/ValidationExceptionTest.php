<?php

namespace WPRemoteMediaExt\Guzzle\Tests\Service\Exception;

use WPRemoteMediaExt\Guzzle\Service\Exception\ValidationException;

class ValidationExceptionTest extends \Guzzle\Tests\GuzzleTestCase
{
    public function testCanSetAndRetrieveErrors()
    {
        $errors = array('foo', 'bar');

        $e = new ValidationException('Foo');
        $e->setErrors($errors);
        $this->assertEquals($errors, $e->getErrors());
    }
}
