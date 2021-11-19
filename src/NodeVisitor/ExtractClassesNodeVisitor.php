<?php

namespace App\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ExtractClassesNodeVisitor extends NodeVisitorAbstract
{
    /** @var Node\Stmt\Class_[] */
    private array $classNodes = [];

    public function reset(): self
    {
        return new self();
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_) {
            $this->classNodes[] = $node;
        }

        return null;
    }

    /** @return Node\Stmt\Class_[] */
    public function getClassNodes(): array
    {
        return $this->classNodes;
    }
}