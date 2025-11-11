<?php
// src/Domain/Entity/User.php

namespace App\Domain\Entity;

use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\Username;
use App\Domain\ValueObject\Password;
use App\Domain\ValueObject\UserRole;

class User
{
    private UserId $id;
    private Username $username;
    private Password $password;
    private UserRole $role;
    private ?string $email;

    public function __construct(
        UserId $id,
        Username $username,
        Password $password,
        UserRole $role,
        ?string $email = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->role = $role;
        $this->email = $email;
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getUsername(): Username
    {
        return $this->username;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    public function getRoleMessage(): string
    {
        return $this->role->isAdmin() 
            ? 'Usuario administrador' 
            : 'Usuario normal';
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return $this->password->verify($plainPassword);
    }
}