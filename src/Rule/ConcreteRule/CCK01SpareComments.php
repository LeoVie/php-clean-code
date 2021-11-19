<?php

namespace App\Rule\ConcreteRule;

use App\Rule\RuleConcept\Rule;
use App\Rule\RuleConcept\RuleTokenSequenceAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class CCK01SpareComments implements RuleTokenSequenceAware
{
    private const NAME = 'CC-K-01 Spare comments';
    private const MAX_AMOUNT_OF_COMMENT_TOKENS_IN_PERCENT = 5;
    private const VIOLATION_MESSAGE_PATTERN = 'File has a too high amount of comment tokens (%f percent points higher than allowed).';

    public function getName(): string
    {
        return self::NAME;
    }

    public function check(TokenSequence $tokenSequence): array
    {
        $commentTokens = (clone $tokenSequence)->onlyComments()->filter();

        $amountOfComments = $this->calculateAmount($commentTokens->length(), $tokenSequence->length());

        if ($amountOfComments > self::MAX_AMOUNT_OF_COMMENT_TOKENS_IN_PERCENT) {
            $message = \Safe\sprintf(
                self::VIOLATION_MESSAGE_PATTERN,
                $amountOfComments - self::MAX_AMOUNT_OF_COMMENT_TOKENS_IN_PERCENT
            );
            return [Violation::create($this, $message)];
        }

        return [Compliance::create($this)];
    }

    private function calculateAmount(int $a, int $b): float
    {
        return ($a / $b) * 100;
    }
}