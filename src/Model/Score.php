<?php

namespace App\Model;

class Score implements \JsonSerializable
{
    private function __construct(
        private string $scoreType,
        private int    $points,
    )
    {
    }

    public static function create(string $scoreType, int $points): self
    {
        return new self($scoreType, $points);
    }

    public function getScoreType(): string
    {
        return $this->scoreType;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function jsonSerialize(): array
    {
        return [
            'score_type' => $this->scoreType,
            'points' => $this->points,
        ];
    }
}