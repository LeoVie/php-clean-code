<?php

namespace App\Scorer;

use App\Model\Score;
use App\Rule\FileRuleResults;

class ViolationsFirstScorer implements Scorer
{
    private const SCORE_TYPE = 'ViolationsFirst';

    public function create(FileRuleResults $fileRuleResults): Score
    {
        return Score::create(self::SCORE_TYPE, 10);
    }
}