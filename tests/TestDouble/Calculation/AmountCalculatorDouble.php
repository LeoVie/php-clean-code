<?php

namespace App\Tests\TestDouble\Calculation;

use App\Calculation\CalculatorConcept\AmountCalculator;

class AmountCalculatorDouble implements AmountCalculator
{
    public function calculate(float $part, float $whole): float
    {
        return 50.0;
    }
}