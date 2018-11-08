<?php

namespace Guillaumesmo\SCDF;

class Sink extends Base
{
    public function __construct()
    {
        parent::__construct(['input']);
    }

    /**
     * @param callable $callback
     * @throws \ErrorException
     */
    public function consume($callback)
    {
        $this->channel->basic_consume($this->queues['input'], '', false, true, false, false, function ($message) use ($callback) {
            $callback($message->body);
        });

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }
}