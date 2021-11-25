<?php

namespace App\Service;

use App\Model\Score;
use App\Rule\FileRuleResults;
use App\Scorer\ViolationsFirstScorer;

class CleanCodeScorerService
{
    public function __construct(
        private ViolationsFirstScorer $violationsFirstScorer
    )
    {
    }

    /** @return Score[] */
    public function createScores(FileRuleResults $fileRuleResults): array
    {
        return [$this->violationsFirstScorer->create($fileRuleResults)];
    }
}