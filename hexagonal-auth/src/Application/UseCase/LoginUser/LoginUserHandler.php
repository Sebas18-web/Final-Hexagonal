<?php

namespace App\Application\UseCase\LoginUser;

use App\Application\DTO\LoginResponse;
use App\Infrastructure\Messaging\RabbitMQ\AuthenticationProducer;

class LoginUserHandler
{
    private AuthenticationProducer $producer;

    public function __construct(AuthenticationProducer $producer)
    {
        $this->producer = $producer;
    }

    public function handle(LoginUserCommand $command): string
    {
        // Generar ID de correlación único
        $correlationId = uniqid('auth_', true);

        // Enviar a RabbitMQ
        $this->producer->publishLoginRequest(
            $command->getUsername(),
            $command->getPassword(),
            $correlationId
        );

        return $correlationId;
    }
}