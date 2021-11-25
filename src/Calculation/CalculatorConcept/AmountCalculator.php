<?php

namespace App\Calculation\CalculatorConcept;

interface AmountCalculator
{
    public function calculate(float $part, float $whole): float;
}