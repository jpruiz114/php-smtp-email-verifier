<?php declare(strict_types=1);

namespace Coco\EmailVerification;

use Coco\EmailVerification\DNS\Constants\Attributes;
use Coco\EmailVerification\DNS\Constants\MxAttributes;
use Coco\EmailVerification\DNS\Constants\RecordTypes;
use Coco\EmailVerification\DNS\Records\MxRecord;

class MxLookup {
    private string $domain;

    /**
     * @param $domain
     */
    public function __construct($domain) {
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
     */
    public function setDomain(string $domain): void
    {
        $this->domain = $domain;
    }

    /**
     * @return array
     */
    public function findMxRecords(): array {
        $dnsRecords = dns_get_record($this->domain, DNS_MX, );

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
     */
    public function getRecordWithHighestPriority(): ?MxRecord {
        $mxRecords = $this->findMxRecords();

        if (empty($mxRecords)) {
            return null;
        }

        usort($mxRecords, function ($item1, $item2) {
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
}
