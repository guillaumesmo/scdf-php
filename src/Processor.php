<?php

namespace Guillaumesmo\SCDF;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Processor extends Base
{
    /**
     * @var AMQPMessage
     */
    private $message;

    public function __construct()
    {
        parent::__construct(['input', 'output']);

        $this->message = new AMQPMessage();
        $this->message->set('application_headers', new AMQPTable([
            'contentType' => 'text/plain',
        ]));
    }

    /**
     * @param callable $callback
     * @throws \ErrorException
     */
    public function process($callback)
    {
        $this->channel->basic_consume($this->queues['input'], '', false, true, false, false, function ($message) use ($callback) {
            $text = $callback($message->body);

            if (is_null($text)) {
                return;
            }

            $this->channel->basic_publish($this->message->setBody($text), '', $this->queues['output']);
        });

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }
}