<?php
// src/Domain/Repository/UserRepositoryInterface.php

namespace App\Domain\Repository;

use App\Domain\Entity\User;
use App\Domain\ValueObject\Username;
use App\Domain\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function findByUsername(Username $username): ?User;
    
    public function findById(UserId $id): ?User;
    
    public function save(User $user): void;
    
    public function recordLoginAttempt(string $username, bool $success): void;
}