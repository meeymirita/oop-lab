<?php
declare(strict_types=1);

final readonly class Money
{
    private const int CENTS_PER_RUB = 100;
    // private-конструктор: снаружи создать Money можно ТОЛЬКО через фабрики ниже
    private function __construct(public int $cents) {}

    public static function rub(int $rubles, int $kopecks = 0): self
    {
        return self::fromCents($rubles * self::CENTS_PER_RUB + $kopecks);
    }
    public static function fromCents(int $cents): self
    {
        if ($cents < 0) {
            throw new InvalidArgumentException('Money cannot be negative: ' . $cents);
        }
        return new self($cents);
    }

    public static function zero(): self
    {
        return new self(0);
    }
    public function add(Money $other): self
    {
        return new self($this->cents + $other->cents);
    }
    public function subtract(Money $other): self
    {
        return self::fromCents($this->cents - $other->cents);
    }
    public function multiply(int $factor): self
    {
        return self::fromCents($this->cents * $factor);
    }
    public function percent(int $p): self
    {
        return new self(intdiv($this->cents * $p, 100));
    }
    public function isGreaterThan(Money $other): bool
    {
        return $this->cents > $other->cents;
    }
    public function equals(Money $other): bool
    {
        return $this->cents === $other->cents;
    }
    public function format(): string
    {
        return number_format($this->cents / 100, 2, ',', ' ') . ' ₽';
    }
}
