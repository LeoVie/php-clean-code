<?php

namespace App\Service;

use App\Find\PhpFileFinder;
use App\NodeVisitor\ExtractClassesNodeVisitor;
use App\NodeVisitor\ExtractNamesNodeVisitor;
use App\Rule\FileRuleResults;
use App\Rule\RuleCollection;
use App\Rule\RuleResult\RuleResultCollection;
use App\Wrapper\LineAndColumnLexerWrapper;
use LeoVie\PhpFilesystem\Service\Filesystem;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

class CleanCodeCheckerService
{
    public function __construct(
        private RuleCollection            $ruleCollection,
        private Filesystem                $filesystem,
        private PhpFileFinder             $phpFileFinder,
        private ExtractClassesNodeVisitor $extractClassesNodeVisitor,
        private LineAndColumnLexerWrapper $lineAndColumnLexerWrapper,
        private ParserFactory             $parserFactory,
        private NodeTraverser             $nodeTraverser,
        private ExtractNamesNodeVisitor   $extractNamesNodeVisitor,
    )
    {
    }

    /** @return FileRuleResults[] */
    public function checkDirectory(string $path): array
    {
        $phpFiles = $this->phpFileFinder->findPhpFilesInPath($path);

        return array_map(
            fn(string $phpFile): FileRuleResults => $this->checkFile($phpFile),
            $phpFiles
        );
    }

    public function checkFile(string $path): FileRuleResults
    {
        $fileCode = $this->filesystem->readFile($path);

        return FileRuleResults::create($path, $this->checkCode($fileCode));
    }

    public function checkCode(string $fileCode): RuleResultCollection
    {
        $ruleResults = [];

        foreach ($this->ruleCollection->getFileCodeAwareRules() as $rule) {
            $ruleResults = array_merge(
                $ruleResults,
                $rule->check($fileCode)
            );
        }

        $lines = explode("\n", $fileCode);
        $lines = array_map(fn(string $line): string => rtrim($line), $lines);
        foreach ($this->ruleCollection->getLinesAwareRules() as $rule) {
            $ruleResults = array_merge(
                $ruleResults,
                $rule->check($lines)
            );
        }

        $tokenSequence = TokenSequence::create(\PhpToken::tokenize($fileCode));
        foreach ($this->ruleCollection->getTokenSequenceAwareRules() as $rule) {
            $ruleResults = array_merge(
                $ruleResults,
                $rule->check($tokenSequence)
            );
        }

        $this->parseAndTraverse($fileCode);
        $classNodes = $this->extractClassesNodeVisitor->getClassNodes();
        foreach ($classNodes as $classNode) {
            foreach ($this->ruleCollection->getClassNodeAwareRules() as $rule) {
                $ruleResults = array_merge(
                    $ruleResults,
                    $rule->check($classNode)
                );
            }
        }

        $nameNodes = $this->extractNamesNodeVisitor->getNameNodes();
        foreach ($nameNodes as $nameNode) {
            foreach ($this->ruleCollection->getNameNodeAwareRules() as $rule) {
                $ruleResults = array_merge(
                    $ruleResults,
                    $rule->check($nameNode)
                );
            }
        }

        return RuleResultCollection::create($ruleResults);
    }

    private function parseAndTraverse(string $fileCode): void
    {
        $parser = $this->parserFactory->create(ParserFactory::PREFER_PHP7, $this->lineAndColumnLexerWrapper->getLexer());

        /** @var Node[] $ast */
        $ast = $parser->parse($fileCode);

        $this->extractClassesNodeVisitor = $this->extractClassesNodeVisitor->reset();
        $this->extractNamesNodeVisitor = $this->extractNamesNodeVisitor->reset();

        $this->nodeTraverser->addVisitor($this->extractClassesNodeVisitor);
        $this->nodeTraverser->addVisitor($this->extractNamesNodeVisitor);

        $this->nodeTraverser->traverse($ast);
    }
}