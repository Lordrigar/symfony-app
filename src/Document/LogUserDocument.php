<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @MongoDB\Document
 */
class LogUserDocument
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"LogUserDocument"})
     */
    protected $name;

    /**
     * @MongoDB\ReferenceMany(targetDocument="LogDocument", mappedBy="logUserDocument")
     */
    protected $logDocuments;

    public function __construct()
    {
        $this->logDocuments = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLogDocuments(): Collection
    {
        return $this->logDocuments;
    }
}