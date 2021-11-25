<?php

namespace App\Tests\TestDouble\Calculation;

use App\Calculation\CalculatorConcept\CriticalityCalculator;

class CriticalityCalculatorDouble implements CriticalityCalculator
{
    public function calculate(float $actual, float $allowed, float $criticalityFactorInPercent): float
    {
        return 10.0;
    }
}