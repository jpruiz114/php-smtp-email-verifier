<?php declare(strict_types=1);

namespace Coco\EmailVerification\Exceptions;

use Exception;

/**
 * Base exception for email validation errors
 */
class EmailValidationException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
