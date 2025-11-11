<?php

namespace App\Domain\Exception;

class UserNotFoundException extends \Exception
{
    public function __construct(string $username)
    {
        parent::__construct("User not found: {$username}");
    }
}
