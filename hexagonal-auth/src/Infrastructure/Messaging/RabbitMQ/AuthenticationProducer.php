<?php

namespace App\Infrastructure\Messaging\RabbitMQ;

use PhpAmqpLib\Message\AMQPMessage;

class AuthenticationProducer
{
    private $channel;

    public function __construct()
    {
        $this->channel = RabbitMQConnection::getChannel();
    }

    public function publishLoginRequest(string $username, string $password, string $correlationId): void
    {
        $messageBody = json_encode([
            'username' => $username,
            'password' => $password,
            'timestamp' => date('Y-m-d H:i:s'),
            'correlation_id' => $correlationId
        ]);

        $message = new AMQPMessage(
            $messageBody,
            [
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                'correlation_id' => $correlationId,
                'reply_to' => 'authentication_response_queue'
            ]
        );

        $this->channel->basic_publish(
            $message,
            '',                        // exchange
            'authentication_queue'     // routing key
        );

        echo " [x] Mensaje enviado a RabbitMQ: {$username}\n";
    }
}