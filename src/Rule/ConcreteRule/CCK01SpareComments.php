<?php

namespace App\Rule\ConcreteRule;

use App\Calculation\CalculatorConcept\AmountCalculator;
use App\Calculation\CalculatorConcept\CriticalityCalculator;
use App\Rule\RuleConcept\RuleTokenSequenceAware;
use App\Rule\RuleResult\Compliance;
use App\Rule\RuleResult\Violation;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class CCK01SpareComments implements RuleTokenSequenceAware
{
    private const NAME = 'CC-K-01 Spare comments';
    private const VIOLATION_MESSAGE_PATTERN = 'File has a too high amount of comment tokens (%f, that\' s %f percent points higher than allowed).';
    private const COMPLIANCE_MESSAGE_PATTERN = 'File has an allowed amount of comment tokens (%f, that\'s %f percent points lower than allowed maximum).';
    private const MAX_AMOUNT_OF_COMMENT_TOKENS_IN_PERCENT = 5;
    private const CRITICALITY_FACTOR = 50;

    public function __construct(
        private CriticalityCalculator $criticalityCalculator,
        private AmountCalculator      $amountCalculator
    )
    {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    private function getMaxAmountOfCommentTokensInPercent(): int
    {
        return self::MAX_AMOUNT_OF_COMMENT_TOKENS_IN_PERCENT;
    }

    private function getCriticalityFactor(): int
    {
        return self::CRITICALITY_FACTOR;
    }

    public function check(TokenSequence $tokenSequence): array
    {
        $commentTokens = $tokenSequence->onlyComments()->filter();

        $amountOfComments = $this->amountCalculator->calculate($commentTokens->length(), $tokenSequence->length());

        if ($amountOfComments > $this->getMaxAmountOfCommentTokensInPercent()) {
            $criticality = $this->criticalityCalculator->calculate(
                $amountOfComments,
                $this->getMaxAmountOfCommentTokensInPercent(),
                $this->getCriticalityFactor()
            );

            $message = $this->buildMessage(self::VIOLATION_MESSAGE_PATTERN, $amountOfComments);

            return [Violation::create($this, $message, $criticality)];
        }

        $message = $this->buildMessage(self::COMPLIANCE_MESSAGE_PATTERN, $amountOfComments);

        return [Compliance::create($this, $message)];
    }

    private function buildMessage(string $pattern, float $amountOfComments): string
    {
        return \Safe\sprintf(
            $pattern,
            $amountOfComments,
            abs($amountOfComments - $this->getMaxAmountOfCommentTokensInPercent())
        );
    }
}