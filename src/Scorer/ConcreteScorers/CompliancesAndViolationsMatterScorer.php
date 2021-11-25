<?php

namespace App\Scorer\ConcreteScorers;

use App\Model\Score;
use App\Rule\FileRuleResults;
use App\Scorer\Scorer;

class CompliancesAndViolationsMatterScorer implements Scorer
{
    private const SCORE_TYPE = 'CompliancesAndViolationsMatter';

    public function create(FileRuleResults $fileRuleResults): Score
    {
        $totalPoints = 0;
        foreach ($fileRuleResults->getRuleResultCollection()->getViolations() as $violation) {
            $totalPoints += $violation->getCriticality();
        }

        $totalPoints /= count($fileRuleResults->getRuleResultCollection()->getRuleResults());

        return Score::create(self::SCORE_TYPE, (int)$totalPoints);
    }
}