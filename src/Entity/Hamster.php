<?php

namespace App\Entity;

use App\Repository\HamsterRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HamsterRepository::class)]
class Hamster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['AllHamsters'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['AllHamsters'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['AllHamsters'])]
    private ?int $hunger = null;

    #[ORM\Column]
    #[Groups(['AllHamsters'])]
    private ?int $age = null;

    #[ORM\Column(length: 1)]
    #[Groups(['AllHamsters'])]
    private ?string $gender = null;

    #[ORM\Column(options: ["default" => true])]
    #[Groups(['AllHamsters'])]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'hamsters')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['AllHamsters'])]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHunger(): ?int
    {
        return $this->hunger;
    }

    public function setHunger(int $hunger): static
    {
        $this->hunger = $hunger;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
