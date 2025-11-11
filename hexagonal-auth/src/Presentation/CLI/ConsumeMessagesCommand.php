<?php

namespace App\Presentation\CLI;

use App\Infrastructure\Messaging\RabbitMQ\AuthenticationConsumer;
use App\Infrastructure\Persistence\MySQLUserRepository;

class ConsumeMessagesCommand
{
    public function execute(): void
    {
        echo "===========================================\n";
        echo "  🐰 RabbitMQ Authentication Consumer\n";
        echo "===========================================\n\n";

        try {
            $userRepository = new MySQLUserRepository();
            $consumer = new AuthenticationConsumer($userRepository);
            
            $consumer->consume();
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }
}