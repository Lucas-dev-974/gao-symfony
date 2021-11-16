<?php

namespace App\Entity;

use App\Repository\AttributionRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AttributionRepository::class)
 */
class Attribution
{


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $horraire;

    /**
     * @ORM\Column(type="date")
     */
    private $date;


    /**
     * @ORM\ManyToOne(targetEntity=Computer::class, inversedBy="attributions")
     */
    private $computer;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="atrtibutions")
     */
    private $user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHorraire(): ?string
    {
        return $this->horraire;
    }

    public function setHorraire(?string $horraire): self
    {
        $this->horraire = $horraire;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getComputer(): ?Computer
    {
        return $this->computer;
    }

    public function setComputer(?Computer $computer): self
    {
        $this->computer = $computer;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user_id = $user;
        return $this;
    }
}
