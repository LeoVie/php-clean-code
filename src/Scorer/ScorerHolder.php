<?php

namespace App\Scorer;

class ScorerHolder
{
    /** @param \Iterator<Scorer> $scorers */
    public function __construct(private iterable $scorers)
    {
    }

    /** @return Scorer[] */
    public function getScorers(): array
    {
        return iterator_to_array($this->scorers);
    }
}