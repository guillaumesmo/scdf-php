<?php

namespace Guillaumesmo\SCDF;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Source extends Base
{
    /**
     * @var AMQPMessage
     */
    private $message;

    public function __construct()
    {
        parent::__construct(['output']);

        $this->message = new AMQPMessage();
        $this->message->set('application_headers', new AMQPTable([
            'contentType' => 'text/plain',
        ]));
    }

    /**
     * @param string $text
     */
    public function produce($text)
    {
        $this->channel->basic_publish($this->message->setBody($text), '', $this->queues['output']);
    }
}