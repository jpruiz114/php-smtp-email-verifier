<?php declare(strict_types=1);

namespace Coco\EmailVerification\DNS\Records;

class MxRecord {
    private string $class;
    private string $host;
    private int $pri;
    private string $target;
    private int $ttl;
    private string $type;

    /**
     * @param string $class
     * @param string $host
     * @param int $pri
     * @param string $target
     * @param int $ttl
     * @param string $type
     */
    public function __construct(string $class, string $host, int $pri, string $target, int $ttl, string $type) {
        $this->class = $class;
        $this->host = $host;
        $this->pri = $pri;
        $this->target = $target;
        $this->ttl = $ttl;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTargetIp(): string {
        $targetIp = gethostbyname($this->target);

        if (empty($targetIp)) {
            echo sprintf(
                'Cannot find the target IP for the domain %s', $this->target
            );
        }

        return $targetIp;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPri(): int
    {
        return $this->pri;
    }

    /**
     * @param int $pri
     */
    public function setPri(int $pri): void
    {
        $this->pri = $pri;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param string $target
     */
    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return $this->ttl;
    }

    /**
     * @param int $ttl
     */
    public function setTtl(int $ttl): void
    {
        $this->ttl = $ttl;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}
