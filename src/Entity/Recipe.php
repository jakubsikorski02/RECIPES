<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $Ingridients = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $howTo = null;

    #[ORM\Column(length: 255)]
    private ?string $imagePath = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'recipes')]
    private Collection $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getIngridients(): ?string
    {
        return $this->Ingridients;
    }

    public function setIngridients(string $Ingridients): static
    {
        $this->Ingridients = $Ingridients;

        return $this;
    }

    public function getHowTo(): ?string
    {
        return $this->howTo;
    }

    public function setHowTo(string $howTo): static
    {
        $this->howTo = $howTo;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): static
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }
    

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

        return $this;
    }
}
