<?php

namespace App\Entity;

use App\Repository\BasketRowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRowRepository::class)]
class BasketRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $quantity;

    #[ORM\Column(type: 'integer')]
    private $subtotal;

    #[ORM\ManyToOne(targetEntity: Basket::class, inversedBy: 'basketRows')]
    #[ORM\JoinColumn(nullable: false)]
    private $basket_id;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'basketRows')]
    #[ORM\JoinColumn(nullable: false)]
    private $product_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getSubtotal(): ?int
    {
        return $this->subtotal;
    }

    // public function setSubtotal(int $subtotal): self
    // {
    //     $this->subtotal = $subtotal;

    //     return $this;
    // }
    public function setSubtotal(): self
    {
        $this->subtotal = $this->product_id->getPrice() * $this->quantity;
        return $this;
    }

    public function getBasketId(): ?Basket
    {
        return $this->basket_id;
    }

    public function setBasketId(?Basket $basket_id): self
    {
        $this->basket_id = $basket_id;

        return $this;
    }

    public function getProductId(): ?Product
    {
        return $this->product_id;
    }

    public function setProductId(?Product $product_id): self
    {
        $this->product_id = $product_id;

        return $this;
    }
}
