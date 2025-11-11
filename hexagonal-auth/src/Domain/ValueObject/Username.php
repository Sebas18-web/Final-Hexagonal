<?php
namespace App\Domain\ValueObject;

class Username
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value) || strlen($value) > 50) {
            throw new \InvalidArgumentException('Invalid username');
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}