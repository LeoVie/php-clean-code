<?php

namespace App\Calculation;

class AmountCalculator implements \App\Calculation\CalculatorConcept\AmountCalculator
{
    public function calculate(float $part, float $whole): float
    {
        return ($part / $whole) * 100;
    }
}