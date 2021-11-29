<?php

namespace App\Parse;

use App\Parse\NodeVisitor\NodeVisitorCollection;
use App\Wrapper\LineAndColumnLexerWrapper;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class ParseAndTraverser
{
    /** @var array<string, NodeVisitorCollection> */
    private array $parseCache = [];

    public function __construct(
        private LineAndColumnLexerWrapper $lineAndColumnLexerWrapper,
        private ParserFactory             $parserFactory,
        private NodeTraverser             $nodeTraverser,
        private NodeVisitorCollection     $nodeVisitorCollection,
    )
    {
    }

    public function parseAndTraverse(string $fileCode): NodeVisitorCollection
    {
        if (array_key_exists($fileCode, $this->parseCache)) {
            return $this->parseCache[$fileCode];
        }

        $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP7, $this->lineAndColumnLexerWrapper->getLexer());

        /** @var Node[] $ast */
        $ast = $parser->parse($fileCode);

        $this->nodeVisitorCollection->resetAll();

        $this->nodeTraverser->addVisitor($this->nodeVisitorCollection->getExtractClassesNodeVisitor());
        $this->nodeTraverser->addVisitor($this->nodeVisitorCollection->getExtractNamesNodeVisitor());

        $this->nodeTraverser->traverse($ast);

        $this->parseCache[$fileCode] = $this->nodeVisitorCollection;

        return $this->parseCache[$fileCode];
    }
}