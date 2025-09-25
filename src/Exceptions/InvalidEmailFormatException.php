<?php declare(strict_types=1);

namespace Coco\EmailVerification\Exceptions;

/**
 * Exception thrown when email format is invalid
 */
class InvalidEmailFormatException extends EmailValidationException
{
    public function __construct(string $email)
    {
        parent::__construct("Invalid email format: {$email}");
    }
}
