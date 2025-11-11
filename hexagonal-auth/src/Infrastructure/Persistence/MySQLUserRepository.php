<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\Username;
use App\Domain\ValueObject\Password;
use App\Domain\ValueObject\UserRole;
use PDO;

class MySQLUserRepository implements UserRepositoryInterface
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = Database::getConnection();
    }

    public function findByUsername(Username $username): ?User
    {
        $stmt = $this->connection->prepare(
            'SELECT id, username, password, role, email FROM users WHERE username = :username'
        );
        
        $stmt->execute(['username' => $username->getValue()]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findById(UserId $id): ?User
    {
        $stmt = $this->connection->prepare(
            'SELECT id, username, password, role, email FROM users WHERE id = :id'
        );
        
        $stmt->execute(['id' => $id->getValue()]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function save(User $user): void
    {
        // Implementación para guardar usuarios
        $stmt = $this->connection->prepare(
            'INSERT INTO users (username, password, role, email) 
             VALUES (:username, :password, :role, :email)
             ON DUPLICATE KEY UPDATE 
             password = :password, role = :role, email = :email'
        );
        
        $stmt->execute([
            'username' => $user->getUsername()->getValue(),
            'password' => $user->getPassword()->getHash(),
            'role' => $user->getRole()->getValue(),
            'email' => $user->getEmail()
        ]);
    }

    public function recordLoginAttempt(string $username, bool $success): void
    {
        $stmt = $this->connection->prepare(
            'INSERT INTO login_attempts (username, success, attempt_date) VALUES (:username, :success, NOW())'
        );
        
        $stmt->execute([
            'username' => $username,
            'success' => $success ? 1 : 0
        ]);
    }

    private function hydrate(array $data): User
    {
        return new User(
            new UserId((int)$data['id']),
            new Username($data['username']),
            Password::fromHash($data['password']),
            new UserRole($data['role']),
            $data['email']
        );
    }
}