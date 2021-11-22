<?php

namespace App\Rule;

use App\Rule\RuleResult\RuleResult;
use App\Rule\RuleResult\Violation;

class FileRuleResults implements \JsonSerializable
{
    /** @param RuleResult[] $ruleResults */
    private function __construct(private string $path, private array $ruleResults)
    {
    }

    /** @param RuleResult[] $ruleResults */
    public static function create(string $path, array $ruleResults): self
    {
        return new self($path, $ruleResults);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    /** @return Violation[] */
    public function getViolations(): array
    {
        return array_values(
            array_filter(
                $this->ruleResults,
                fn(RuleResult $rr): bool => $rr instanceof Violation
            )
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'path' => $this->path,
            'violations' => array_map(
                fn(RuleResult $rr): array => $rr->jsonSerialize(),
                $this->getViolations()
            ),
        ];
    }

    public function toString(): string
    {
        return \Safe\sprintf(
            "%s:\n\t%s",
            $this->path,
            join("\n\t", array_map(fn(RuleResult $rr): string => $rr->toString(), $this->getViolations()))
        );
    }
}