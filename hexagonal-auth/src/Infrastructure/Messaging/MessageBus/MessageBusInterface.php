<?php

namespace App\Infrastructure\Messaging\MessageBus;

interface MessageBusInterface
{
    public function dispatch(object $message): void;
}