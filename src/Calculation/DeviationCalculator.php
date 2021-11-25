<?php

namespace App\Calculation;

use App\Calculation\CalculatorConcept\AmountCalculator;

class DeviationCalculator
{
    public function __construct(private AmountCalculator $amountCalculator)
    {
    }

    public function calculateRelativeDeviation(float $actual, float $allowed): float
    {
        return ceil($this->amountCalculator->calculate($this->calculateAbsoluteDeviation($actual, $allowed), $allowed));
    }

    public function calculateAbsoluteDeviation(float $actual, float $allowed): float
    {
        return abs($actual - $allowed);
    }
}