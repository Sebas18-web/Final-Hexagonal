<?php

namespace App\Infrastructure\Messaging\RabbitMQ;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQConnection
{
    private static ?AMQPStreamConnection $connection = null;
    private static ?AMQPChannel $channel = null;

    public static function getChannel(): AMQPChannel
    {
        if (self::$connection === null) {
            self::$connection = new AMQPStreamConnection(
                'localhost',  // host
                5672,         // port
                'guest',      // user
                'guest'       // password
            );
        }

        if (self::$channel === null) {
            self::$channel = self::$connection->channel();
            
            // Declarar la cola
            self::$channel->queue_declare(
                'authentication_queue',  // nombre de la cola
                false,                   // passive
                true,                    // durable
                false,                   // exclusive
                false                    // auto_delete
            );
        }

        return self::$channel;
    }

    public static function close(): void
    {
        if (self::$channel !== null) {
            self::$channel->close();
        }
        if (self::$connection !== null) {
            self::$connection->close();
        }
    }
}
