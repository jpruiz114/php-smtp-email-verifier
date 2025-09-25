<?php declare(strict_types=1);

namespace Coco\EmailVerification\Exceptions;

/**
 * Exception thrown when SMTP connection fails
 */
class SmtpConnectionException extends EmailValidationException
{
    public function __construct(string $host, int $port, string $errorMessage = "", int $errorCode = 0)
    {
        $message = "Failed to connect to SMTP server {$host}:{$port}";
        if (!empty($errorMessage)) {
            $message .= ". Error: {$errorMessage} (Code: {$errorCode})";
        }
        parent::__construct($message, $errorCode);
    }
}
