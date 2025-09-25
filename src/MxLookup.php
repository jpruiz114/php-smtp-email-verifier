<?php declare(strict_types=1);

namespace Coco\EmailVerification;

use Coco\EmailVerification\DNS\Constants\Attributes;
use Coco\EmailVerification\DNS\Constants\MxAttributes;
use Coco\EmailVerification\DNS\Constants\RecordTypes;
use Coco\EmailVerification\DNS\Records\MxRecord;
use Coco\EmailVerification\Exceptions\InvalidDomainException;
use Coco\EmailVerification\Exceptions\DnsLookupException;

class MxLookup {
    private string $domain;

    /**
     * @param string $domain
     * @throws InvalidDomainException
     */
    public function __construct(string $domain) {
        if (empty($domain) || !$this->isValidDomain($domain)) {
            throw new InvalidDomainException($domain);
        }
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @throws InvalidDomainException
     */
    public function setDomain(string $domain): void
    {
        if (empty($domain) || !$this->isValidDomain($domain)) {
            throw new InvalidDomainException($domain);
        }
        $this->domain = $domain;
    }

    /**
     * @return array
     * @throws DnsLookupException
     */
    public function findMxRecords(): array {
        $dnsRecords = dns_get_record($this->domain, DNS_MX);

        if ($dnsRecords === false) {
            throw new DnsLookupException($this->domain, "DNS query failed");
        }

        $mxRecords = [];

        foreach ($dnsRecords as $dnsRecord) {
            if (!empty($dnsRecord[Attributes::ATTRIBUTE_TYPE])) {
                $dnsRecordType = $dnsRecord[Attributes::ATTRIBUTE_TYPE];

                if ($dnsRecordType == RecordTypes::MX) {
                    $mxRecords[] = $dnsRecord;
                }
            }
        }

        return $mxRecords;
    }

    /**
     * @return MxRecord|null
     * @throws DnsLookupException
     */
    public function getRecordWithHighestPriority(): ?MxRecord {
        $mxRecords = $this->findMxRecords();

        if (empty($mxRecords)) {
            throw new DnsLookupException($this->domain, "No MX records found");
        }

        usort($mxRecords, function (array $item1, array $item2): int {
            return $item1[MxAttributes::ATTRIBUTE_PRI] <=> $item2[MxAttributes::ATTRIBUTE_PRI];
        });

        $firstMxRecord = $mxRecords[0];

        return new MxRecord(
            $firstMxRecord[Attributes::ATTRIBUTE_CLASS],
            $firstMxRecord[Attributes::ATTRIBUTE_HOST],
            $firstMxRecord[MxAttributes::ATTRIBUTE_PRI],
            $firstMxRecord[MxAttributes::ATTRIBUTE_TARGET],
            $firstMxRecord[Attributes::ATTRIBUTE_TTL],
            $firstMxRecord[Attributes::ATTRIBUTE_TYPE],
        );
    }

    /**
     * Validates if a domain name is valid
     * @param string $domain
     * @return bool
     */
    private function isValidDomain(string $domain): bool {
        return filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }
}
