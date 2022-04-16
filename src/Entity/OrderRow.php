<?php

namespace App\Entity;

use App\Repository\OrderRowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRowRepository::class)]
class OrderRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $quantity;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderRows', cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $order_id;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orderRows', cascade:['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    #[ORM\Column(type: 'float')]
    private $subtotal;

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

    public function getOrderId(): ?Order
    {
        return $this->order_id;
    }

    public function setOrderId(?Order $order_id): self
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSubtotal(): ?int
    {
        return $this->subtotal;
    }

    public function setSubtotal(): self
    {
        $this->subtotal = $this->product->getPrice() * $this->quantity;
        return $this;
    }
}
