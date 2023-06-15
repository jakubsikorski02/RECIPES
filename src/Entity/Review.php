<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $User;

    #[ORM\ManyToMany(targetEntity: Recipe::class)]
    private Collection $Recipe;

    public function __construct()
    {
        $this->User = new ArrayCollection();
        $this->Recipe = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->User;
    }

    public function addUser(User $user): static
    {
        if (!$this->User->contains($user)) {
            $this->User->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->User->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipe(): Collection
    {
        return $this->Recipe;
    }

    public function addRecipe(Recipe $recipe): static
    {
        if (!$this->Recipe->contains($recipe)) {
            $this->Recipe->add($recipe);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): static
    {
        $this->Recipe->removeElement($recipe);

        return $this;
    }
}
