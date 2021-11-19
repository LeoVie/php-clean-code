<?php

namespace App\Rule;

use App\Rule\RuleConcept\Rule;
use App\Rule\RuleConcept\RuleClassNodeAware;
use App\Rule\RuleConcept\RuleFileCodeAware;
use App\Rule\RuleConcept\RuleNameNodeAware;
use App\Rule\RuleConcept\RuleTokenSequenceAware;

class RuleCollection
{
    /** @var Rule[][] */
    private array $rules = [];

    /** @param iterable<RuleClassNodeAware|RuleFileCodeAware|RuleTokenSequenceAware|RuleNameNodeAware> $rules */
    public function __construct(iterable $rules)
    {
        foreach ($rules as $rule) {
            $this->rules[$rule::AWARE_OF][] = $rule;
        }
    }

    /** @return RuleClassNodeAware[] */
    public function getClassNodeAwareRules(): array
    {
        /** @var RuleClassNodeAware[] $rules */
        $rules = $this->rules[Rule::CLASS_NODE_AWARE];

        return $rules;
    }

    /** @return RuleFileCodeAware[] */
    public function getFileCodeAwareRules(): array
    {
        /** @var RuleFileCodeAware[] $rules */
        $rules = $this->rules[Rule::FILE_CODE_AWARE];

        return $rules;
    }

    /** @return RuleTokenSequenceAware[] */
    public function getTokenSequenceAwareRules(): array
    {
        /** @var RuleTokenSequenceAware[] $rules */
        $rules = $this->rules[Rule::TOKEN_SEQUENCE_AWARE];

        return $rules;
    }

    /** @return RuleNameNodeAware[] */
    public function getNameNodeAwareRules(): array
    {
        /** @var RuleNameNodeAware[] $rules */
        $rules = $this->rules[Rule::NAME_NODE_AWARE];

        return $rules;
    }
}