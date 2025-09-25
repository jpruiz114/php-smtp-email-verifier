<?php declare(strict_types=1);

/**
 * Email Verification Demo Script
 * 
 * This script demonstrates the email verification functionality
 * by testing various email addresses with different scenarios.
 */

// Load the library classes manually (since no composer dependencies are required)
require_once __DIR__ . '/src/EmailValidator.php';
require_once __DIR__ . '/src/MxLookup.php';
require_once __DIR__ . '/src/DNS/Records/MxRecord.php';
require_once __DIR__ . '/src/DNS/Constants/Attributes.php';
require_once __DIR__ . '/src/DNS/Constants/MxAttributes.php';
require_once __DIR__ . '/src/DNS/Constants/RecordTypes.php';
require_once __DIR__ . '/src/Exceptions/EmailValidationException.php';
require_once __DIR__ . '/src/Exceptions/InvalidEmailFormatException.php';
require_once __DIR__ . '/src/Exceptions/DnsLookupException.php';
require_once __DIR__ . '/src/Exceptions/SmtpConnectionException.php';
require_once __DIR__ . '/src/Exceptions/InvalidDomainException.php';

use Coco\EmailVerification\EmailValidator;
use Coco\EmailVerification\MxLookup;

//$result = dns_get_record("gmail.com");
//print_r($result);

/*getmxrr('gmail.com', $mxhosts);
print_r($mxhosts);

getmxrr('activecampaign.com', $mxhosts);
print_r($mxhosts);

getmxrr('hotmail.com', $mxhosts);
print_r($mxhosts);

getmxrr('nasa.gov', $mxhosts);
print_r($mxhosts);*/

/*$mxLookup = new MxLookup("gmail.com");
$records = $mxLookup->findMxRecords();
echo print_r($records, true);

$highestPriorityRecord = $mxLookup->getRecordWithHighestPriority();
echo print_r($highestPriorityRecord, true);

$socket = fsockopen($highestPriorityRecord->getTargetIp(), 25, $error_code, $error_message, 5);

if (!empty($error_code)) {
    echo $error_message . PHP_EOL;
    exit;
}

echo fgets($socket, 1024) . PHP_EOL;

function send_command(&$socket, $command) {
    echo $command;
    fputs($socket, $command);

    while (true) {
        $line = fgets($socket, 4096);

        echo $line;

        $status = socket_get_status($socket);

        if ($status && $status['unread_bytes'] == 0) {
            break;
        }
    }

    echo PHP_EOL;
}

$command = sprintf("EHLO %s\r\n", $mxLookup->getDomain());
send_command($socket, $command);

$command = "MAIL FROM:<jpruiz114@gmail.com>\r\n";
send_command($socket, $command);

$command = "RCPT TO:<jpruiz114@gmail.com>\r\n";
send_command($socket, $command);

$command = "MAIL FROM:<superawesomeguy1492@gmail.com>\r\n";
send_command($socket, $command);

$command = "RCPT TO:<superawesomeguy1492@gmail.com>\r\n";
send_command($socket, $command);

$command = "QUIT\r\n";
send_command($socket, $command);*/

$NS=array('8.8.8.8');
$dns_NS_array = dns_get_record("google.com", DNS_NS, $NS);
//print_r($dns_NS_array);

$emailValidator = new EmailValidator();

// Use well-known domains with reliable SMTP servers for testing
$emails = [
    "test@gmail.com",                      // Google - very reliable
    "test@outlook.com",                    // Microsoft - very reliable  
    "test@yahoo.com",                      // Yahoo - generally reliable
    "invalid@nonexistentdomain12345.fake", // Should fail with DNS error
    "invalid-email-format",                // Should fail with format error
    "sue@yourdreamsredeemed.com",
    "contato@raquelvanin.com",
    "taylor@eccentricentrepreneurs.com",
    "jpruiz114@gmail.com",
    "super.hero000001122@gmail.com",
    "derp.derpson@activecampaign.com",
    "jpruiz114@yahoo.com",
    "jpruiz114@hotmail.com",
    "derp.derpson@gmail.com",
    "homer.simpson@nasa.gov",
    "info@hypersciences.com",
    "ContactCEA@cea.eop.gov",
    "comments@whitehouse.gov",
];

echo "=== Email Verification Demo ===" . PHP_EOL;
echo "Testing with reliable domains and expected failures..." . PHP_EOL . PHP_EOL;

foreach ($emails as $index => $email) {
    echo "[" . ($index + 1) . "/" . count($emails) . "] Testing: $email" . PHP_EOL;
    
    $startTime = microtime(true);
    
    try {
        // Set a reasonable timeout for each test
        $result = $emailValidator->verifyEmailAddress($email);
        
        $duration = round(microtime(true) - $startTime, 2);
        
        if ($result) {
            echo "✅ VALID - $email (took {$duration}s)" . PHP_EOL;
        } else {
            echo "❌ INVALID - $email (took {$duration}s)" . PHP_EOL;
        }
        
    } catch (Throwable $exception) {
        $duration = round(microtime(true) - $startTime, 2);
        echo "⚠️  ERROR - " . get_class($exception) . ": " . $exception->getMessage() . " (took {$duration}s)" . PHP_EOL;
    }
    
    echo "---" . PHP_EOL;
}

echo PHP_EOL . "=== Demo completed ===" . PHP_EOL;
echo "Note: Some tests may show as 'invalid' even for real emails because:" . PHP_EOL;
echo "- Many mail servers block verification attempts" . PHP_EOL;
echo "- Firewall rules may prevent SMTP connections" . PHP_EOL;
echo "- Some servers don't respond to RCPT TO verification" . PHP_EOL;
