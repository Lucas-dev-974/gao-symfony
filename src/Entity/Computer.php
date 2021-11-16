<?php

namespace App\Entity;

use App\Repository\ComputerRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ComputerRepository::class)
 */
class Computer
{

    public static $date;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity=Attribution::class, mappedBy="computer", orphanRemoval=true)
     */
    protected $attributions;

    public function __construct()
    {
        $this->attributions = new ArrayCollection();
    }

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

    public function AddAttribution(Attribution $attribution){
        if(!$this->attribution->contains($attribution)){
            $this->attribution[] = $attribution;
            $attribution->setComputer($this);
        }

        return $this;
    }

    public function getAttributions($date): Collection
    {   
        return $this->attributions;
    }

    public function deleteAttribution(Attribution $attribution): self
    {
        if ($this->attribution->removeElement($attribution)) {
            // set the owning side to null (unless already changed)
            if ($attribution->getComputer() === $this) {
                $attribution->setComputer(null);
            }
        }

        return $this;
    }
}
