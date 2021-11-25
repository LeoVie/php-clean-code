<?php

namespace App\Rule;

use App\Rule\RuleResult\RuleResultCollection;

class FileRuleResults implements \JsonSerializable
{
    private function __construct(private string $path, private RuleResultCollection $ruleResultCollection)
    {
    }

    public static function create(string $path, RuleResultCollection $ruleResultCollection): self
    {
        return new self($path, $ruleResultCollection);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getRuleResultCollection(): RuleResultCollection
    {
        return $this->ruleResultCollection;
    }

    public function jsonSerialize(): array
    {
        return [
            'path' => $this->path,
            'rule_results' => $this->ruleResultCollection->jsonSerialize()
        ];
    }
}