<?php

namespace Guillaumesmo\SCDF;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

abstract class Base
{
    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var array
     */
    protected $queues = [];

    /**
     * @var string
     */
    private $appLabel;

    /**
     * @var string
     */
    private $streamName;

    /**
     * @param array $bindings
     */
    public function __construct(array $bindings)
    {
        $longopts = array_map(function ($binding) {
            return "spring.cloud.stream.bindings.$binding.destination:";
        }, $bindings);

        $longopts[] = 'spring.cloud.dataflow.stream.app.label:';
        $longopts[] = 'spring.cloud.dataflow.stream.name:';

        $options = getopt('', $longopts);
        $this->appLabel = $options['spring.cloud.dataflow.stream.app.label'];
        $this->streamName = $options['spring.cloud.dataflow.stream.name'];

        foreach ($bindings as $binding) {
            $destination = $options["spring.cloud.stream.bindings.$binding.destination"];
            $this->queues[$binding] = "$destination.{$this->streamName}";
        }

        $connection = new AMQPStreamConnection(
            getenv('SPRING_RABBITMQ_HOST'),
            getenv('SPRING_RABBITMQ_PORT'),
            'guest',
            'guest'
        );

        $this->channel = $connection->channel();
    }
}