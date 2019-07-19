<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeImmutable;

/**
 * @MongoDB\Document
 */
class LogDocument
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"LogDocument"})
     */
    protected $message;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"LogDocument"})
     */
    protected $timestamp;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $time
     * @return LogDocument
     */
    public function setTimestamp(string $time): LogDocument
    {
        $this->timestamp = $time;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return LogDocument
     */
    public function setMessage(string $message): LogDocument
    {
        $this->message = $message;

        return $this;
    }
}