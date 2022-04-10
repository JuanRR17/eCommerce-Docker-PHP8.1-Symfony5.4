<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $total;

    #[ORM\OneToMany(mappedBy: 'basket_id', targetEntity: BasketRow::class, orphanRemoval: true)]
    private $basketRows;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'baskets')]
    private $userid;

    public function __construct()
    {
        $this->basketRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    // public function setTotal(?int $total): self
    // {
    //     $this->total = $total;

    //     return $this;
    // }
    public function setTotal(?int $total=0):self
    {
        if(!empty($this->getBasketRows())){
            foreach($this->getBasketRows() as $row ){
                $total += $row->getSubtotal();
            }
        }
        $this->total = $total;
        return $this;
    }

    /**
     * @return Collection<int, BasketRow>
     */
    public function getBasketRows(): Collection
    {
        return $this->basketRows;
    }

    public function addBasketRow(BasketRow $basketRow): self
    {
        if (!$this->basketRows->contains($basketRow)) {
            $this->basketRows[] = $basketRow;
            $basketRow->setBasketId($this);
        }

        return $this;
    }

    public function removeBasketRow(BasketRow $basketRow): self
    {
        if ($this->basketRows->removeElement($basketRow)) {
            // set the owning side to null (unless already changed)
            if ($basketRow->getBasketId() === $this) {
                $basketRow->setBasketId(null);
            }
        }

        return $this;
    }

    public function getUserid(): ?User
    {
        return $this->userid;
    }

    public function setUserid(?User $userid): self
    {
        $this->userid = $userid;

        return $this;
    }
}
