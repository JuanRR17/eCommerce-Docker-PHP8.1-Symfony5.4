<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    // #[ORM\Column(type: 'datetime_immutable')]
    // private $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\Column(type: 'float')]
    private $cost;

    #[ORM\Column(type: 'string', length: 255)]
    private $address;

    #[ORM\Column(type: 'string', length: 255)]
    private $city;

    #[ORM\Column(type: 'string', length: 255)]
    private $country;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\OneToMany(mappedBy: 'order_id', targetEntity: OrderRow::class, orphanRemoval: true)]
    private $orderRows;

    #[ORM\Column(type: 'string', length: 255)]
    private $receiverName;

    #[ORM\Column(type: 'string', length: 255)]
    private $receiverSurname;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    public function __construct()
    {
        $this->orderRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    // public function setCost(float $cost): self
    // {
    //     $this->cost = $cost;

    //     return $this;
    // }

    public function setCost(?float $total=0): self
    {
        if(!empty($this->getOrderRows())){
            foreach($this->getOrderRows() as $row ){
                $total += $row->getSubtotal();
            }
        }
        $this->cost = $total;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, OrderRow>
     */
    public function getOrderRows(): Collection
    {
        return $this->orderRows;
    }

    public function addOrderRow(OrderRow $orderRow): self
    {
        if (!$this->orderRows->contains($orderRow)) {
            $this->orderRows[] = $orderRow;
            $orderRow->setOrderId($this);
        }

        return $this;
    }

    public function removeOrderRow(OrderRow $orderRow): self
    {
        if ($this->orderRows->removeElement($orderRow)) {
            // set the owning side to null (unless already changed)
            if ($orderRow->getOrderId() === $this) {
                $orderRow->setOrderId(null);
            }
        }

        return $this;
    }

    public function getReceiverName(): ?string
    {
        return $this->receiverName;
    }

    public function setReceiverName(string $receiverName): self
    {
        $this->receiverName = $receiverName;

        return $this;
    }

    public function getReceiverSurname(): ?string
    {
        return $this->receiverSurname;
    }

    public function setReceiverSurname(string $receiverSurname): self
    {
        $this->receiverSurname = $receiverSurname;

        return $this;
    }
}
