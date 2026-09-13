<?php
declare(strict_types=1);

require __DIR__ . '/Drinks.php';

// ── Шаг В: полиморфизм ────────────────────────────────────────────────────
$menu = [
    new Espresso(Size::Small),
    new Latte(Size::Large),
    new Cappuccino(Size::Medium),
    new Tea(Size::Large)
];

foreach ($menu as $drink) {                              // одна строка — четыре разных поведения
    printf("%-40s %s\n", $drink->name(), $drink->price()->format());
}

// new Drink(Size::Small);           // Error: Cannot instantiate abstract class Drink
// new Espresso(Size::Large);        // DomainException: Эспрессо не бывает большим
// Size::from('XL');


