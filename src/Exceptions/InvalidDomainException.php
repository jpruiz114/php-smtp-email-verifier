<?php declare(strict_types=1);

namespace Coco\EmailVerification\Exceptions;

/**
 * Exception thrown when domain is invalid
 */
class InvalidDomainException extends EmailValidationException
{
    public function __construct(string $domain)
    {
        parent::__construct("Invalid domain: {$domain}");
    }
}
