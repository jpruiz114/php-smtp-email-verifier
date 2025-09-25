<?php declare(strict_types=1);

require_once __DIR__ . './../vendor/autoload.php';

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

$emails = [
    "sue@yourdreamsredeemed.com",
    "contato@raquelvanin.com",
    "taylor@eccentricentrepreneurs.com",
    /*"jpruiz114@gmail.com",
    "super.hero000001122@gmail.com",
    "derp.derpson@activecampaign.com",
    "jpruiz114@yahoo.com",
    "jpruiz114@hotmail.com",
    "derp.derpson@gmail.com",
    "homer.simpson@nasa.gov",
    "info@hypersciences.com",
    "ContactCEA@cea.eop.gov",
    "comments@whitehouse.gov",*/
];

foreach ($emails as $email) {
    try {
        if ($emailValidator->verifyEmailAddress($email)) {
            echo $email . " is valid" . PHP_EOL;
        }
        else {
            echo $email . " is not valid" . PHP_EOL;
        }
    }
    catch (Throwable $exception) {
        echo $exception->getMessage() . PHP_EOL;
    }
}
