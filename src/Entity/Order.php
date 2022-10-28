<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $OrderDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $User;

    /**
     * @ORM\Column(type="integer")
     */
    private $Total;

    /**
     * @ORM\OneToMany(targetEntity=OrderDetail::class, mappedBy="Ord")
     */
    private $Product;

    public function __construct()
    {
        $this->Product = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->OrderDate;
    }

    public function setOrderDate(\DateTimeInterface $OrderDate): self
    {
        $this->OrderDate = $OrderDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->Total;
    }

    public function setTotal(int $Total): self
    {
        $this->Total = $Total;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getProduct(): Collection
    {
        return $this->Product;
    }

    public function addProduct(OrderDetail $product): self
    {
        if (!$this->Product->contains($product)) {
            $this->Product[] = $product;
            $product->setOrd($this);
        }

        return $this;
    }

    public function removeProduct(OrderDetail $product): self
    {
        if ($this->Product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getOrd() === $this) {
                $product->setOrd(null);
            }
        }

        return $this;
    }
}
