# Email Verification Library

A PHP library for comprehensive email address verification that goes beyond basic syntax validation by performing DNS lookups and SMTP server connections to verify email deliverability.

## Features

- **Syntax Validation**: Validates email format using PHP's built-in filters
- **DNS MX Record Lookup**: Queries DNS to find mail exchange servers for the email domain
- **SMTP Connection Testing**: Connects to actual mail servers to verify email address existence
- **Priority-based Server Selection**: Automatically selects the highest priority mail server
- **Detailed Response Tracking**: Captures SMTP server responses for debugging
- **RFC 5321 Compliance**: Full compliance with [RFC 5321](https://tools.ietf.org/html/rfc5321) SMTP standards
- **Robust Error Handling**: Comprehensive exception handling for all failure scenarios

## How It Works

1. **Format Check**: Validates the email address syntax
2. **Domain Extraction**: Extracts the domain portion from the email address
3. **DNS Lookup**: Performs MX record lookup to find mail servers for the domain
4. **Server Selection**: Chooses the highest priority (lowest priority number) mail server
5. **SMTP Connection**: Establishes connection to the mail server on port 25
6. **SMTP Handshake**: Performs EHLO, MAIL FROM, and RCPT TO commands
7. **Response Analysis**: Analyzes SMTP response codes to determine email validity

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd email-verification
```

2. Install dependencies:
```bash
composer install
```

## Usage

### Basic Email Verification

```php
<?php
require_once 'vendor/autoload.php';

use Coco\EmailVerification\EmailValidator;
use Coco\EmailVerification\Exceptions\InvalidEmailFormatException;
use Coco\EmailVerification\Exceptions\DnsLookupException;
use Coco\EmailVerification\Exceptions\SmtpConnectionException;

$validator = new EmailValidator();

try {
    $email = "user@example.com";
    if ($validator->verifyEmailAddress($email)) {
        echo "Email is valid and deliverable";
    } else {
        echo "Email is invalid or not deliverable";
    }
} catch (InvalidEmailFormatException $e) {
    echo "Invalid email format: " . $e->getMessage();
} catch (DnsLookupException $e) {
    echo "DNS lookup failed: " . $e->getMessage();
} catch (SmtpConnectionException $e) {
    echo "SMTP connection failed: " . $e->getMessage();
}
```

### Advanced Usage with Response Details

```php
$validator = new EmailValidator();
$email = "user@example.com";

$isValid = $validator->verifyEmailAddress($email);

// Get detailed SMTP responses
$connectionResult = $validator->getConnectionResult();
$ehloResult = $validator->getEhloResult();
$mailFromResult = $validator->getMailFromResult();
$recipientResult = $validator->getRecipientResult();

// Process results...
```

### MX Record Lookup Only

```php
use Coco\EmailVerification\MxLookup;
use Coco\EmailVerification\Exceptions\InvalidDomainException;
use Coco\EmailVerification\Exceptions\DnsLookupException;

try {
    $mxLookup = new MxLookup("example.com");
    $mxRecords = $mxLookup->findMxRecords();
    $highestPriorityRecord = $mxLookup->getRecordWithHighestPriority();
} catch (InvalidDomainException $e) {
    echo "Invalid domain: " . $e->getMessage();
} catch (DnsLookupException $e) {
    echo "DNS lookup failed: " . $e->getMessage();
}
```

## Running Tests

The project includes test cases to verify functionality:

```bash
php src/MxLookupTest.php
```

Run PHPUnit tests:

```bash
vendor/bin/phpunit
```

## Requirements

- PHP 7.4 or higher
- Network access for DNS lookups and SMTP connections
- Ability to make outbound connections on port 25

## Exception Handling

The library uses custom exceptions for different error scenarios:

### Exception Types

- **`InvalidEmailFormatException`**: Thrown when email format is invalid
- **`InvalidDomainException`**: Thrown when domain name is invalid
- **`DnsLookupException`**: Thrown when DNS lookup fails or no MX records found
- **`SmtpConnectionException`**: Thrown when SMTP connection fails
- **`EmailValidationException`**: Base exception class for all email validation errors

### Exception Usage

```php
try {
    $result = $validator->verifyEmailAddress($email);
} catch (InvalidEmailFormatException $e) {
    // Handle invalid email format
} catch (DnsLookupException $e) {
    // Handle DNS/MX record issues
} catch (SmtpConnectionException $e) {
    // Handle SMTP connection problems
} catch (EmailValidationException $e) {
    // Handle any other email validation error
}
```

## SMTP Protocol Compliance

The library follows [RFC 5321](https://tools.ietf.org/html/rfc5321) SMTP standards for all email verification operations:

### SMTP Commands Used
- **EHLO**: Extended SMTP greeting to identify the client
- **MAIL FROM**: Specifies the sender's email address
- **RCPT TO**: Specifies the recipient's email address
- **QUIT**: Properly terminates the SMTP session

### Command Format
All SMTP commands use proper `\r\n` (CRLF) line endings as required by [RFC 5321](https://tools.ietf.org/html/rfc5321). This ensures maximum compatibility with mail servers and adherence to internet standards.

### SMTP Response Codes

The library recognizes standard SMTP response codes:

- `250`: Command successful (email exists)
- `550`: Non-existent email address
- `550-5.1.1`: Bad destination mailbox address

## Important Notes

- **Network Requirements**: This library requires network access to perform DNS lookups and SMTP connections
- **Firewall Considerations**: Ensure outbound connections on port 25 are allowed
- **Rate Limiting**: Some mail servers may rate limit or block verification attempts
- **Production Use**: Consider implementing caching and retry logic for production environments
- **Standards Compliance**: The library strictly adheres to [RFC 5321](https://tools.ietf.org/html/rfc5321) SMTP protocol standards
- **Error Handling**: Comprehensive exception handling provides detailed error information for debugging

## Project Structure

```
src/
├── EmailValidator.php          # Main validation class
├── MxLookup.php               # DNS MX record lookup
├── MxLookupTest.php           # Test cases
├── DNS/
│   ├── Records/
│   │   └── MxRecord.php       # MX record data structure
│   └── Constants/
│       ├── Attributes.php     # DNS record attributes
│       ├── MxAttributes.php   # MX-specific attributes
│       └── RecordTypes.php    # DNS record types
└── Exceptions/
    ├── EmailValidationException.php        # Base exception
    ├── InvalidEmailFormatException.php     # Invalid email format
    ├── InvalidDomainException.php          # Invalid domain
    ├── DnsLookupException.php              # DNS lookup failures
    └── SmtpConnectionException.php         # SMTP connection failures
```

## Author

**Jean-Paul Ruiz**  
Email: jpruiz114@gmail.com

## License

This project is open source. Please check the license file for more details.

---

**Disclaimer**: This library performs actual SMTP connections to verify email addresses. Use responsibly and in accordance with the terms of service of the mail servers you're connecting to.
