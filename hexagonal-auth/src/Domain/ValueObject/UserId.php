<?php
namespace App\Domain\ValueObject;

class UserId
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('User ID must be positive');
        }
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}