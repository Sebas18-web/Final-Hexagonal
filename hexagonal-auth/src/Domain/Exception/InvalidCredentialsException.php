<?php

namespace App\Domain\Exception;

class InvalidCredentialsException extends \Exception
{
    public function __construct()
    {
        parent::__construct("Invalid credentials provided");
    }
}