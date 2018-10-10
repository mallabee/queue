<?php

namespace Mallabee\Queue\Drivers\Sqs;

use Aws\Sqs\SqsClient;
use Mallabee\Queue\Core\Arr;
use Mallabee\Queue\Core\QueueConnectorInterface;
use Mallabee\Queue\Core\QueueInterface;

class SqsConnector implements QueueConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array  $config
     * @return QueueInterface
     */
    public function connect(array $config)
    {
        $config = $this->getDefaultConfiguration($config);

        if ($config['key'] && $config['secret']) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return new SqsQueue(
            new SqsClient($config), $config['queue'], $config['prefix'] ?? ''
        );
    }

    /**
     * Get the default configuration for SQS.
     *
     * @param  array  $config
     * @return array
     */
    protected function getDefaultConfiguration(array $config)
    {
        return array_merge([
            'version' => 'latest',
            'http' => [
                'timeout' => 60,
                'connect_timeout' => 60,
            ],
        ], $config);
    }
}
