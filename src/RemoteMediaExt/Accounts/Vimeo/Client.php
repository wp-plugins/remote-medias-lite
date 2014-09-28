<?php
namespace WPRemoteMediaExt\RemoteMediaExt\Accounts\Vimeo;

use WPRemoteMediaExt\RemoteMediaExt\Accounts\AbstractRemoteClient;
use WPRemoteMediaExt\Guzzle\Service\Description\ServiceDescription;
use WPRemoteMediaExt\Guzzle\Common\Collection;

class Client extends AbstractRemoteClient
{
    /**
     * Factory method to create a new VimeoClient
     *
     */
    public static function factory($config = array())
    {

        $default  = array(
            'base_url' => '{scheme}://vimeo.com/api/{version}/',
            'scheme'   => 'http',
            'version'  => 'v2',
            'curl.options' => array(
                CURLOPT_TIMEOUT   => 30,
                CURLOPT_CONNECTTIMEOUT   => 30,
            )
        );
        $required = array('base_url');

        $config = Collection::fromConfig($config, $default, $required);

        $client = new self($config->get('base_url'), $config);

        // Attach a service description to the client
        $description = ServiceDescription::factory(__DIR__ . '/ServiceDescription.json');
        $client->setDescription($description);

        return $client;
    }
}
