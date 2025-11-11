<?php

namespace App\Application\DTO;

class LoginResponse
{
    public bool $success;
    public string $message;
    public ?int $userId;
    public ?string $username;
    public ?string $role;

    public function __construct(
        bool $success,
        string $message,
        ?int $userId = null,
        ?string $username = null,
        ?string $role = null
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->userId = $userId;
        $this->username = $username;
        $this->role = $role;
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'user_id' => $this->userId,
            'username' => $this->username,
            'role' => $this->role
        ];
    }
}