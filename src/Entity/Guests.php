<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuestsRepository")
 */
class Guests
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Guests"})
     * @Assert\NotNull
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Guests"})
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"Guests"})
     */
    private $nickName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickname(string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }
}
