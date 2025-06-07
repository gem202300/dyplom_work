<?php

namespace App\ValueObjects;

use Illuminate\Support\Number;

class Price
{
    public readonly int $stored;

    public readonly float $value;

    public readonly string $formatted;

    public function __construct(int $price)
    {
        $this->stored = $price;
        $this->value = round($this->stored / 100, 2);
        // $this->formatted = Number::currency($price, in: config('app.currency'));
        $this->formatted = Number::format(
            $this->value,
            precision: 2,
            locale: config('app.locale')
        ).' zł';
    }

    public static function from(int $price): self
    {
        return new self($price);
    }

    public static function create(float $price): self
    {
        return new self(round($price * 100));
    }
}
