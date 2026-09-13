<?php
declare(strict_types=1);
require __DIR__ . '/Money.php';

$latte = Money::rub(250, 333);
$oat = Money::rub(60,333);
$price = $latte->add($oat)->multiply(2);

echo $latte->format(), "\n";          // 250,00 ₽  — не изменился после add()
echo $price->format(), "\n";          // 620,00 ₽
echo $price->percent(10)->format(), "\n";   // 62,00 ₽

//var_dump(Money::rub(5)->equals(Money::rub(5)));   // true  — равенство по содержимому
//var_dump(Money::rub(5) === Money::rub(5));
