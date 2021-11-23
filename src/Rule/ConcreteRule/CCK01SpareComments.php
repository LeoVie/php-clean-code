<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\RuleTokenSequenceAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class CCK01SpareComments implements RuleTokenSequenceAware
{
    private const NAME = 'CC-K-01 Spare comments';
    private const MAX_AMOUNT_OF_COMMENT_TOKENS_IN_PERCENT = 5;
    private const VIOLATION_MESSAGE_PATTERN = 'File has a too high amount of comment tokens (%f, that\' s %f percent points higher than allowed).';
    private const COMPLIANCE_MESSAGE_PATTERN = 'File has an allowed amount of comment tokens (%f, that\'s %f percent points lower than allowed maximum).';

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(TokenSequence $tokenSequence): array
    {
        $commentTokens = $tokenSequence->onlyComments()->filter();

        $amountOfComments = $this->calculateAmount($commentTokens->length(), $tokenSequence->length());

        if ($amountOfComments > self::MAX_AMOUNT_OF_COMMENT_TOKENS_IN_PERCENT) {
            $message = $this->buildMessage(self::VIOLATION_MESSAGE_PATTERN, $amountOfComments);

            return [Violation::create($this, $message)];
        }

        $message = $this->buildMessage(self::COMPLIANCE_MESSAGE_PATTERN, $amountOfComments);

        return [Compliance::create($this, $message)];
    }

    private function calculateAmount(int $a, int $b): float
    {
        return ($a / $b) * 100;
    }

    private function buildMessage(string $pattern, float $amountOfComments): string
    {
        return \Safe\sprintf(
            $pattern,
            $amountOfComments,
            abs($amountOfComments - self::MAX_AMOUNT_OF_COMMENT_TOKENS_IN_PERCENT)
        );
    }
}