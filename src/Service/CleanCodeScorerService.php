<?php

namespace App\Service;

use App\Model\Score;
use App\Rule\FileRuleResults;
use App\Scorer\Scorer;
use App\Scorer\ScorerHolder;

class CleanCodeScorerService
{
    public function __construct(private ScorerHolder $scorerHolder)
    {
    }

    /** @return Score[] */
    public function createScores(FileRuleResults $fileRuleResults): array
    {
        return array_map(
            fn(Scorer $scorer): Score => $scorer->create($fileRuleResults),
            $this->scorerHolder->getScorers()
        );
    }
}