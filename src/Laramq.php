<?php

namespace Dodysat\Laramq;

use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class Laramq
{
    public $channel;
    public $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(env('RABBITMQ_HOST'), env('RABBITMQ_PORT'), env('RABBITMQ_USERNAME'), env('RABBITMQ_PASSWORD'), env('RABBITMQ_VHOST'));
        $this->channel = $this->connection->channel();
    }

    public function queue($queue, $data, $exchange = null)
    {
        $data = json_encode($data);

        $this->channel->queue_declare($queue, false, true, false, false);
        $message = new AMQPMessage($data, [
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            'expiration' => 1 * 1000 * 60 * 60 * 24 * 3
        ]);
        $this->channel->basic_publish($message, $exchange, $queue);
        $this->channel->close();
        $this->connection->close();
        return;
    }
}
