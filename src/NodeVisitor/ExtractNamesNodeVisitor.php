<?php

namespace App\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ExtractNamesNodeVisitor extends NodeVisitorAbstract
{
    /** @var array<Node\Identifier|Node\Expr\Variable> */
    private array $nameNodes = [];

    public function reset(): self
    {
        return new self();
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Expr\Variable) {
            $this->nameNodes[] = $node;
        } else if (
            $node instanceof Node\Stmt\Function_
            || $node instanceof Node\Const_
            || $node instanceof Node\Stmt\Class_
            || $node instanceof Node\Stmt\ClassMethod
        ) {
            if ($node->name === null) {
                throw new \Exception('Name is null');
            }

            $this->nameNodes[] = $node->name;
        }

        return null;
    }

    /** @return array<Node\Identifier|Node\Expr\Variable> */
    public function getNameNodes(): array
    {
        return $this->nameNodes;
    }
}