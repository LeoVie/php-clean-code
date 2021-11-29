<?php

namespace App\Service;

use App\Find\PhpFileFinder;
use App\Parse\ParseAndTraverser;
use App\Rule\FileRuleResults;
use App\Rule\RuleCollection;
use App\Rule\RuleResult\RuleResult;
use App\Rule\RuleResult\RuleResultCollection;
use LeoVie\PhpFilesystem\Service\Filesystem;
use LeoVie\PhpTokenNormalize\Model\TokenSequence;

class CleanCodeCheckerService
{
    public function __construct(
        private RuleCollection    $ruleCollection,
        private Filesystem        $filesystem,
        private PhpFileFinder     $phpFileFinder,
        private ParseAndTraverser $parseAndTraverser
    )
    {
    }

    public function getRuleCollection(): RuleCollection
    {
        return $this->ruleCollection;
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
        return RuleResultCollection::create(array_merge(
            $this->checkFileCodeAwareRules($fileCode),
            $this->checkLinesAwareRules($fileCode),
            $this->checkTokenSequenceAwareRules($fileCode),
            $this->checkClassNodeAwareRules($fileCode),
            $this->checkNameNodeAwareRules($fileCode)
        ));
    }

    /** @return RuleResult[] */
    public function checkFileCodeAwareRules(string $fileCode): array
    {
        $ruleResults = [];
        foreach ($this->ruleCollection->getFileCodeAwareRules() as $rule) {
            $ruleResults = array_merge(
                $ruleResults,
                $rule->check($fileCode)
            );
        }

        return $ruleResults;
    }

    /** @return RuleResult[] */
    public function checkLinesAwareRules(string $fileCode): array
    {
        $ruleResults = [];
        $lines = explode("\n", $fileCode);
        $lines = array_map(fn(string $line): string => rtrim($line), $lines);
        foreach ($this->ruleCollection->getLinesAwareRules() as $rule) {
            $ruleResults = array_merge(
                $ruleResults,
                $rule->check($lines)
            );
        }

        return $ruleResults;
    }

    /** @return RuleResult[] */
    public function checkTokenSequenceAwareRules(string $fileCode): array
    {
        $ruleResults = [];
        $tokenSequence = TokenSequence::create(\PhpToken::tokenize($fileCode));
        foreach ($this->ruleCollection->getTokenSequenceAwareRules() as $rule) {
            $ruleResults = array_merge(
                $ruleResults,
                $rule->check($tokenSequence)
            );
        }

        return $ruleResults;
    }

    /** @return RuleResult[] */
    public function checkClassNodeAwareRules(string $fileCode): array
    {
        $ruleResults = [];
        $classNodes = $this->parseAndTraverser
            ->parseAndTraverse($fileCode)
            ->getExtractClassesNodeVisitor()
            ->getClassNodes();
        foreach ($classNodes as $classNode) {
            foreach ($this->ruleCollection->getClassNodeAwareRules() as $rule) {
                $ruleResults = array_merge(
                    $ruleResults,
                    $rule->check($classNode)
                );
            }
        }

        return $ruleResults;
    }

    /** @return RuleResult[] */
    public function checkNameNodeAwareRules(string $fileCode): array
    {
        $ruleResults = [];
        $nameNodes = $this->parseAndTraverser
            ->parseAndTraverse($fileCode)
            ->getExtractNamesNodeVisitor()
            ->getNameNodes();
        foreach ($nameNodes as $nameNode) {
            foreach ($this->ruleCollection->getNameNodeAwareRules() as $rule) {
                $ruleResults = array_merge(
                    $ruleResults,
                    $rule->check($nameNode)
                );
            }
        }

        return $ruleResults;
    }
}