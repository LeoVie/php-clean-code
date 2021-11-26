<?php

namespace App\Model;

use App\Rule\FileRuleResults;

class ScoresResult
{
    /** @param Score[] $scores */
    private function __construct(
        private FileRuleResults $fileRuleResults,
        private array           $scores
    )
    {
    }

    /** @param Score[] $scores */
    public static function create(FileRuleResults $fileRuleResults, array $scores): self
    {
        return new self($fileRuleResults, $scores);
    }

    public function getFileRuleResults(): FileRuleResults
    {
        return $this->fileRuleResults;
    }

    /** @return Score[] */
    public function getScores(): array
    {
        return $this->scores;
    }
}