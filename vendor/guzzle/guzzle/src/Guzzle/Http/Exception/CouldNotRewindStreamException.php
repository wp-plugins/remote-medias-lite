<?php

namespace WPRemoteMediaExt\Guzzle\Http\Exception;

use WPRemoteMediaExt\Guzzle\Common\Exception\RuntimeException;

class CouldNotRewindStreamException extends RuntimeException implements HttpException {}
