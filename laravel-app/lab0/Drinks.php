<?php
declare(strict_types=1);
require_once __DIR__ . '/Money.php';

enum Size: string
{
    case Small = 'S';
    case Medium = 'M';
    case Large = 'L';

    public function surcharge(): Money
    {
        return match ($this) {
            self::Small => Money::zero(),
            self::Medium => Money::rub(40),
            self::Large => Money::rub(80)
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Small => 'маленький',
            self::Medium => 'средний',
            self::Large => 'большой'
        };
    }
}

// ── Шаг А: два класса «в лоб» ─────────────────────────────────────────────
final class LatteV1
{
    public function __construct(private readonly Size $size)
    {
    }

    public function name(): string
    {
        return 'Латте (' . $this->size->label() . ')';
    }

    public function price(): Money
    {
        return Money::rub(250)->add($this->size->surcharge());
    }
}

final class EspressoV1
{
    public function __construct(private readonly Size $size)
    {
    }

    public function name(): string
    {
        return 'Эспрессо (' . $this->size->label() . ')';
    }

    public function price(): Money
    {
        return Money::rub(150)->add($this->size->surcharge());
    }
}

// Разница между классами — два литерала: название и базовая цена. Всё остальное — копипаста.
// С четырьмя напитками это 4 копии; с изменением формулы цены — 4 правки.

// ── Шаг Б: общее — в абстрактного родителя ────────────────────────────────
abstract class Drink
{
    public function __construct(protected readonly Size $size)
    {
    }

    abstract protected function basePrice(): Money;     // дырка: обязательна

    abstract protected function title(): string;        // дырка: обязательна

    protected function note(): string
    {
        return '';
    }    // хук: по желанию

    final public function price(): Money                // скелет: одинаков для всех, менять нельзя
    {
        return $this->basePrice()->add($this->size->surcharge());
    }

    final public function name(): string
    {
        $name = "{$this->title()} ({$this->size->label()})";
        return $this->note() === '' ? $name : "{$name}, {$this->note()}";
    }
}

final class Latte extends Drink
{
    protected function basePrice(): Money
    {
        return Money::rub(250);
    }

    protected function title(): string
    {
        return 'Латте';
    }
}

final class Cappuccino extends Drink
{
    protected function basePrice(): Money
    {
        return Money::rub(230);
    }

    protected function title(): string
    {
        return 'Капучино';
    }
}

final class Tea extends Drink
{
    protected function basePrice(): Money
    {
        return Money::rub(120);
    }

    protected function title(): string
    {
        return 'Чай';
    }

    protected function note(): string
    {
        return 'заваривается 4 минуты';
    }   // единственный, кому нужен хук
}

final class Espresso extends Drink
{
    public function __construct(Size $size)
    {
        if ($size === Size::Large) {
            throw new DomainException('Эспрессо не бывает большим');   // инвариант — в конструкторе, обойти нельзя
        }
        parent::__construct($size);
    }

    protected function basePrice(): Money
    {
        return Money::rub(150);
    }

    protected function title(): string
    {
        return 'Эспрессо';
    }
}
