<?php
declare(strict_types=1);
require_once __DIR__ . '/Drinks.php';

final readonly class OrderLine
{
    public function __construct(public string $name, public Money $unitPrice, public int $qty)
    {
    }

    public function total(): Money
    {
        return $this->unitPrice->multiply($this->qty);
    }
}

final class Order
{
    /** @var list<OrderLine> */
    private array $lines = [];                 // единственное изменяемое состояние — и оно private
    private bool $paid = false;

    public function add(Drink $drink, int $qty = 1): void
    {
        if ($this->paid) {
            throw new DomainException('Заказ уже оплачен, добавлять нельзя');
        }
        if ($qty < 1) {
            throw new InvalidArgumentException("Количество должно быть ≥ 1, получено {$qty}");
        }
        $this->lines[] = new OrderLine($drink->name(), $drink->price(), $qty);
    }

    public function total(): Money
    {
        $sum = Money::zero();
        foreach ($this->lines as $line) {
            $sum = $sum->add($line->total());
        }
        return $sum;
    }

    public function pay(): void
    {
        if ($this->lines === []) {
            throw new DomainException('Нельзя оплатить пустой заказ');
        }
        $this->paid = true;
    }

    /** @return list<OrderLine> — массив копируется, снаружи изменить внутренний нельзя */
    public function lines(): array
    {
        return $this->lines;
    }
}

$order = new Order();
$order->add(new Latte(Size::Large), 2);
$order->add(new Espresso(Size::Small));
//$order->add(new Tea(Size::Small), -2);
$order->add(new Tea(Size::Small));
foreach ($order->lines() as $line) {
    printf("%d × %-25s %s\n", $line->qty, $line->name, $line->total()->format());
}
echo 'Итого: ', $order->total()->format(), "\n";
$order->pay();

// попытки сломать — по одной:
// $order->add(new Tea(Size::Small), -2);     // InvalidArgumentException: Количество должно быть ≥ 1
// $order->add(new Tea(Size::Small));         // DomainException: Заказ уже оплачен
// $order->lines[] = 'мусор';                 // Error: Cannot access private property Order::$lines
// $lines = $order->lines(); $lines[] = 'мусор'; echo count($order->lines());   // 2 — копия, оригинал цел
