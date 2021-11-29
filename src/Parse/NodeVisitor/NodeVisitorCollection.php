<?php

namespace App\Parse\NodeVisitor;

class NodeVisitorCollection
{
    public function __construct(
        private ExtractClassesNodeVisitor $extractClassesNodeVisitor,
        private ExtractNamesNodeVisitor   $extractNamesNodeVisitor
    )
    {
    }

    public function getExtractClassesNodeVisitor(): ExtractClassesNodeVisitor
    {
        return $this->extractClassesNodeVisitor;
    }

    public function getExtractNamesNodeVisitor(): ExtractNamesNodeVisitor
    {
        return $this->extractNamesNodeVisitor;
    }

    public function resetAll(): self
    {
        $this->extractClassesNodeVisitor = $this->extractClassesNodeVisitor->reset();
        $this->extractNamesNodeVisitor = $this->extractNamesNodeVisitor->reset();

        return $this;
    }
}