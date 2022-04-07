<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $model;

    #[ORM\Column(type: 'text', nullable: true)]
    private $specifications;

    #[ORM\Column(type: 'float')]
    private $price;

    #[ORM\Column(type: 'integer')]
    private $stock;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $offer;

    #[ORM\Column(type: 'string', length: 255)]
    private $colour;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private $category;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private $brand;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Image::class, orphanRemoval: true)]
    #[ORM\OrderBy(['isDefault' => 'DESC'])]
    private $images;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderRow::class)]
    private $orderRows;

    #[ORM\OneToMany(mappedBy: 'product_id', targetEntity: BasketRow::class)]
    private $basketRows;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->orderRows = new ArrayCollection();
        $this->basketRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getSpecifications(): ?string
    {
        return $this->specifications;
    }

    public function setSpecifications(?string $specifications): self
    {
        $this->specifications = $specifications;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getOffer(): ?int
    {
        return $this->offer;
    }

    public function setOffer(?int $offer): self
    {
        $this->offer = $offer;

        return $this;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setColour(string $colour): self
    {
        $this->colour = $colour;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

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
            $orderRow->setProduct($this);
        }

        return $this;
    }

    public function removeOrderRow(OrderRow $orderRow): self
    {
        if ($this->orderRows->removeElement($orderRow)) {
            // set the owning side to null (unless already changed)
            if ($orderRow->getProduct() === $this) {
                $orderRow->setProduct(null);
            }
        }

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
            $basketRow->setProductId($this);
        }

        return $this;
    }

    public function removeBasketRow(BasketRow $basketRow): self
    {
        if ($this->basketRows->removeElement($basketRow)) {
            // set the owning side to null (unless already changed)
            if ($basketRow->getProductId() === $this) {
                $basketRow->setProductId(null);
            }
        }

        return $this;
    }
}
