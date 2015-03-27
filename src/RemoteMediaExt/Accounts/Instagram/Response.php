<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Instagram;

use WPRemoteMediaExt\Guzzle\Service\Command\ResponseClassInterface;
use WPRemoteMediaExt\Guzzle\Service\Command\OperationCommand;

class Response implements ResponseClassInterface
{
    public static function fromCommand(OperationCommand $command)
    {
        $response = $command->getResponse();

        $html = $response->getBody(true);
        // $html = strstr($html, '{"static_root');
        // $html = strstr($html, '</script>', true);
        // $html = substr($html, 0, -1);

        return json_decode($html);
    }
}
