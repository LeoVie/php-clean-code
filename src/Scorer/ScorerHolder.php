<?php

namespace App\Scorer;

use App\Model\Score;

class ScorerHolder
{
    /** @param iterable<Score> $scorers */
    public function __construct(private iterable $scorers)
    {
    }

    /** @return Score[] */
    public function getScorers(): array
    {
        return iterator_to_array($this->scorers);
    }
}