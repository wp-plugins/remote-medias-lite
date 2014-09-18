<?php

namespace WPRemoteMediaExt\Guzzle\Plugin\Cache;

use WPRemoteMediaExt\Guzzle\Http\Message\RequestInterface;
use WPRemoteMediaExt\Guzzle\Http\Message\Response;

/**
 * Never performs cache revalidation and just assumes the request is still ok
 */
class SkipRevalidation extends DefaultRevalidation
{
    public function __construct() {}

    public function revalidate(RequestInterface $request, Response $response)
    {
        return true;
    }
}
