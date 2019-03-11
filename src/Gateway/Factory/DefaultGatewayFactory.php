<?php

namespace SixBySix\RealtimeDespatch\Gateway\Factory;

use SixBySix\RealtimeDespatch\Api\Credentials;
use SixBySix\RealtimeDespatch\Gateway\DefaultGateway;
use SixBySix\RealtimeDespatch\Middleware\ExceptionMiddleware;

use Buzz\Browser as HttpClient;
use Buzz\Client\Curl as HttpCurlAdapter;
use Buzz\Middleware\BasicAuthMiddleware as BasicAuthMiddleware;

/**
 * Default Gateway Factory.
 */
class DefaultGatewayFactory
{
    /**
     * Creates a new default gateway instance
     *
     * @param \SixBySix\RealtimeDespatch\Api\Credentials $credentials
     *
     * @return \SixBySix\RealtimeDespatch\Gateway\DefaultGateway
     */
    public function create(Credentials $credentials)
    {
        $adapter = new HttpCurlAdapter();
        $adapter->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $adapter->setOption(CURLOPT_TIMEOUT, 300);

        $client = new HttpClient($adapter);

        $client->addMiddleware(
            new BasicAuthMiddleware(
                $credentials->getUsername(),
                $credentials->getPassword()
            )
        );

        $client->addMiddleware(new ExceptionMiddleware);

        $params = array(
            'query' => array(
                'channel'      => $credentials->getChannel(),
                'organisation' => $credentials->getOrganisation()
            )
        );

        return new DefaultGateway($client, $credentials->getEndpoint(), $params);
    }
}