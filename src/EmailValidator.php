<?php declare(strict_types=1);

namespace Coco\EmailVerification;

class EmailValidator {
    public const SMTP_PORT = 25;

    /**
     * @param $socket
     * @param string $command
     * @return array
     */
    private function sendCommand(&$socket, string $command): array {
        $result = [];

        fputs($socket, $command);

        while (true) {
            $line = fgets($socket, 4096);
            $result[] = $line;

            $status = socket_get_status($socket);

            if ($status && $status['unread_bytes'] == 0) {
                break;
            }
        }

        return $result;
    }

    private array $connectionResult;
    private array $ehloResult;
    private array $mailFromResult;
    private array $recipientResult;

    /**
     * @return array
     */
    public function getConnectionResult(): array
    {
        return $this->connectionResult;
    }

    /**
     * @return array
     */
    public function getEhloResult(): array
    {
        return $this->ehloResult;
    }

    /**
     * @return array
     */
    public function getMailFromResult(): array
    {
        return $this->mailFromResult;
    }

    /**
     * @return array
     */
    public function getRecipientResult(): array
    {
        return $this->recipientResult;
    }

    /**
     * @param array $result
     * @return array
     */
    private function getCodes(array $result): array {
        $codes = [];

        foreach ($result as $currentLine) {
            if (empty($currentLine)) {
                continue;
            }

            $parts = explode(" ", $currentLine);
            $codes[] = $parts[0];
        }

        return array_unique($codes);
    }

    public const SMTP_CODE_SERVER_READY = '200';
    public const SMTP_CODE_OKAY = '250';
    public const SMTP_CODE_NON_EXISTENT_EMAIL_ADDRESS = '550';
    public const SMTP_CODE_NON_EXISTENT_EMAIL_ADDRESS_BAD_DESTINATION_MAILBOX_ADDRESS = '550-5.1.1';

    /**
     * @param string $emailAddress
     * @return bool
     */
    public function verifyEmailAddress(string $emailAddress): bool {
        $this->connectionResult = [];
        $this->ehloResult = [];
        $this->mailFromResult = [];
        $this->recipientResult = [];

        if (filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            $parts = explode("@", $emailAddress);
            $domain = $parts[1];
        }
        else {
            return false;
        }

        $mxLookup = new MxLookup($domain);

        $highestPriorityRecord = $mxLookup->getRecordWithHighestPriority();
        echo print_r($highestPriorityRecord, true);

        if (empty($highestPriorityRecord)) {
            echo sprintf(
                'Cannot find highest priority record for the domain %s' . PHP_EOL, $domain
            );

            return false;
        }

        /** @var resource|false $socket */
        $socket = fsockopen(
            $highestPriorityRecord->getTargetIp(), self::SMTP_PORT, $error_code, $error_message, 5
        );

        if (!empty($error_code)) {
            return false;
        }

        $this->connectionResult[] = fgets($socket, 1024) . PHP_EOL;
        echo print_r($this->connectionResult, true);

        $command = sprintf("EHLO %s\r\n", $mxLookup->getDomain());
        $this->ehloResult = $this->sendCommand($socket, $command);
        echo print_r($this->ehloResult, true);

        $command = sprintf("MAIL FROM:<%s>\r\n", $emailAddress);
        $this->mailFromResult = $this->sendCommand($socket, $command);
        $mailFromCodes = $this->getCodes($this->mailFromResult);
        echo print_r($mailFromCodes, true);

        $command = sprintf("RCPT TO:<%s>\r\n", $emailAddress);
        $this->recipientResult = $this->sendCommand($socket, $command);
        $recipientCodes = $this->getCodes($this->recipientResult);
        echo print_r($recipientCodes, true);

        $isValid = false;
        if (sizeof($recipientCodes) == 1 && $recipientCodes[0] == self::SMTP_CODE_OKAY) {
            $isValid = true;
        }

        // Close the socket to prevent resource leak
        fclose($socket);

        return $isValid;
    }
}
