<?php

namespace App\Scorer;

use App\Model\Score;
use App\Rule\FileRuleResults;

interface Scorer
{
    public function create(FileRuleResults $fileRuleResults): Score;
}