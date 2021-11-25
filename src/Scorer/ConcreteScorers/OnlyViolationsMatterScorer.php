<?php

namespace App\Scorer\ConcreteScorers;

use App\Model\Score;
use App\Rule\FileRuleResults;
use App\Scorer\Scorer;

class OnlyViolationsMatterScorer implements Scorer
{
    private const SCORE_TYPE = 'OnlyViolationsMatter';

    public function create(FileRuleResults $fileRuleResults): Score
    {
        if (count($fileRuleResults->getRuleResultCollection()->getViolations()) === 0) {
            return Score::create(self::SCORE_TYPE, 0);
        }

        $totalPoints = 0;
        foreach ($fileRuleResults->getRuleResultCollection()->getViolations() as $violation) {
            $totalPoints += $violation->getCriticality();
        }

        $totalPoints /= count($fileRuleResults->getRuleResultCollection()->getViolations());

        return Score::create(self::SCORE_TYPE, (int)$totalPoints);
    }
}