<?php
namespace App\Domain\ValueObject;

class UserRole
{
    private const ADMIN = 'admin';
    private const USER = 'user';

    private string $value;

    public function __construct(string $value)
    {
        if (!in_array($value, [self::ADMIN, self::USER])) {
            throw new \InvalidArgumentException('Invalid role');
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isAdmin(): bool
    {
        return $this->value === self::ADMIN;
    }

    public function isUser(): bool
    {
        return $this->value === self::USER;
    }
}
