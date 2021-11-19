<?php

namespace App\Rule\RuleConcept;

use App\Rule\RuleResult\RuleResult;
use PhpParser\Node\Stmt\Class_;

interface RuleClassNodeAware extends Rule
{
    public const AWARE_OF = Rule::CLASS_NODE_AWARE;

    /** @return RuleResult[] */
    public function check(Class_ $class): array;
}