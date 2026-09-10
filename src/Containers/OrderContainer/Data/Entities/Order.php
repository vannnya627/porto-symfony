<?php

declare(strict_types=1);

namespace App\Containers\OrderContainer\Data\Entities;

use App\Containers\OrderContainer\Enum\OrderStatus;
use App\Ship\Parents\Entities\Entity;
use App\Ship\ValueObjects\Price;
use App\Ship\ValueObjects\Quantity;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity]
#[ORM\Table(name: '`order`')]
final class Order extends Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public private(set) int $id;

    #[ORM\Column]
    public private(set) int $userId;

    #[ORM\Column(nullable: false)]
    public private(set) int $totalPrice {
        set(int $value) {
            if ($value < 0) {
                throw new InvalidArgumentException("Загальна вартість не може бути від'ємною");
            }
            $this->totalPrice = $value;
        }
    }

    #[ORM\Column(enumType: OrderStatus::class)]
    public private(set) OrderStatus $status = OrderStatus::NEW;

    #[ORM\Column]
    public private(set) ?DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    public private(set) ?DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(
        targetEntity: OrderItem::class,
        mappedBy: 'order',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    public private(set) Collection $orderItems;

    private function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        $this->orderItems->removeElement($orderItem);

        return $this;
    }

    private function recalculateTotalPrice(): void
    {
        $total = 0;
        foreach ($this->orderItems as $item) {
            $total += $item->price->value * $item->quantity->value;
        }
        $this->totalPrice = $total;
    }

    public static function create(int $userId): static
    {
        $order = new self();
        $order->userId = $userId;

        return $order;
    }

    public function addItem(int $productId, string $productName, Quantity $quantity, Price $price): void
    {
        $orderItem = OrderItem::create($this, $productId, $productName, $quantity, $price);

        $this->orderItems->add($orderItem);

        $this->recalculateTotalPrice();
    }
}
