<?php declare(strict_types=1);

namespace Coco\EmailVerification\Exceptions;

/**
 * Exception thrown when DNS lookup fails
 */
class DnsLookupException extends EmailValidationException
{
    public function __construct(string $domain, string $reason = "")
    {
        $message = "DNS lookup failed for domain: {$domain}";
        if (!empty($reason)) {
            $message .= ". Reason: {$reason}";
        }
        parent::__construct($message);
    }
}
