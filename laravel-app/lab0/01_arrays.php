<?php
declare(strict_types=1);

function itemPrice(array $item): int
{
    $base = match ($item['drink']) {
        'espresso' => 150,
        'latte' => 250,
        'cappuccino' => 230,
        'tea' => 120
    };
    $size = match ($item['size']) {
        'S' => 0,
        'M' => 40,
        'L' => 80,
    };
    $extras = 0;

    foreach ($item['extras'] as $e) {
        $extras += match ($e) {
            'oat' => 60,
            'shot' => 70,
            'vanilla' => 40
        };
    }
    return ($base + $size + $extras) * $item['qty'];
}

function orderTotal(array $items): int
{
    return array_sum(array_map(itemPrice(...), $items));
}

$order = [
    [
        'drink' => 'latte',
        'size' => 'L',
        'extras' => ['oat', 'shot'],
        'qty' => 2
    ],
    [
        'drink' => 'espresso',
        'size' => 'L',
        'extras' => [],
        'qty' => 1
    ],
];

echo 'itogo: ' . orderTotal($order) . "R\n";
